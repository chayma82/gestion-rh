<?php

namespace App\Models\Concerns;

use App\Models\Log;

/**
 * Trait à ajouter sur n'importe quel modèle Eloquent pour journaliser
 * automatiquement ses créations, modifications et suppressions dans
 * la table `log`, SANS toucher aux contrôleurs.
 *
 * Fonctionne pour toute action passant par une INSTANCE Eloquent :
 *   $conge->save() / Conge::create() / $conge->update() / $conge->delete()
 *
 * IMPORTANT : beaucoup d'actions de votre app ne sont PAS de vraies
 * suppressions mais des changements de statut via update() (archiver,
 * désarchiver, résilier, marquer payée...). Pour que ces actions restent
 * lisibles dans le log (et pas juste "Modification sur employe #9"),
 * ce trait détecte automatiquement les champs qui ont changé et les
 * affiche dans la description, ex :
 *   "Modification sur employe #9 (statutEmploye: attente_prise_poste → archive) par ..."
 *
 * ⚠️ SÉCURITÉ : ce trait est déclenché sur des modèles créés/modifiés
 * dans des flux PUBLICS non authentifiés (ex: Tenant/Entreprise/Utilisateur
 * créés pendant l'inscription publique via AuthController::store(), ou
 * Utilisateur modifié pendant ForgotPasswordController::reinitialiser()).
 * Si vos helpers current_utilisateur() / current_tenant_id() lèvent une
 * exception ou font un abort() quand personne n'est connecté (plutôt que
 * de retourner simplement null), le journal ne doit JAMAIS faire planter
 * l'action principale (inscription, réinitialisation de mot de passe...).
 * D'où le try/catch ci-dessous : le log est un effet secondaire, pas une
 * condition de succès de l'action.
 *
 * ⚠️ Limite : un update() de masse sur le Query Builder ne déclenche PAS
 * les événements de modèle, donc n'est PAS journalisé automatiquement.
 * Exemple concret dans votre code : SalaireController::payerTous()
 * utilise Salaire::where(...)->update([...]) → pas de log automatique.
 * Si vous voulez logger ce cas précis, il faut soit boucler sur les
 * instances (comme le fait déjà TenantController::valider()), soit
 * ajouter un Log::enregistrer() manuel juste après.
 *
 * Placez ce fichier dans : app/Models/Concerns/Loggable.php
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait Loggable
{
    /**
     * Champs à ne jamais afficher dans le détail du log (bruit inutile
     * ou données sensibles).
     */
    protected array $loggableChampsIgnores = [
        'updated_at',
        'date_modification',
        'motdepasse',
        'remember_token',
    ];

    public static function bootLoggable(): void
    {
        static::created(function ($model) {
            $model->tenterEnregistrerLog('Création');
        });

        static::updated(function ($model) {
            // getChanges() est déjà disponible ici : Eloquent appelle
            // syncChanges() juste avant de déclencher l'événement 'updated'.
            $model->tenterEnregistrerLog('Modification', $model->getChanges());
        });

        static::deleted(function ($model) {
            $model->tenterEnregistrerLog('Suppression');
        });
    }

    /**
     * Enveloppe enregistrerLog() dans un try/catch : le journal d'audit
     * ne doit JAMAIS faire échouer l'action principale (création,
     * modification, suppression), même si les helpers current_utilisateur()
     * / current_tenant_id() se comportent de façon inattendue (abort,
     * exception) dans un contexte non authentifié.
     */
    protected function tenterEnregistrerLog(string $action, array $changements = []): void
    {
        try {
            $this->enregistrerLog($action, $changements);
        } catch (\Throwable $e) {
            // On avale volontairement l'erreur : logger dans error_log plutôt
            // que de casser le flux principal. Si le log applicatif échoue,
            // ce n'est jamais une raison de bloquer l'inscription, la
            // réinitialisation de mot de passe, etc.
            report($e);
        }
    }

    protected function enregistrerLog(string $action, array $changements = []): void
    {
        $acteur = $this->recupererActeurConnecteEnSecurite();

        $tenantId = $this->tenant_id
            ?? $this->recupererTenantIdEnSecurite();

        $description = "{$action} sur " . $this->getTable() . " #{$this->getKey()}";

        $detailChangements = $this->formaterChangements($changements);

        if ($detailChangements !== '') {
            $description .= " ({$detailChangements})";
        }

        if ($acteur) {
            // nom_complet si l'accesseur existe sur le modèle Utilisateur,
            // sinon on reconstruit à partir de prenom + nom.
            $nomActeur = $acteur->nom_complet ?? trim("{$acteur->prenom} {$acteur->nom}");

            $description .= " par {$nomActeur} ({$acteur->email})";
        }

        Log::enregistrer(
            tenantId: $tenantId,
            utilisateurId: $acteur?->id,
            recordId: $this->getKey(),
            nomTable: $this->getTable(),
            description: $description,
        );
    }

    /**
     * Retourne current_utilisateur() si disponible, ou null en cas
     * d'absence de session / d'exception levée par le helper (contexte
     * public non authentifié : inscription, mot de passe oublié...).
     */
    protected function recupererActeurConnecteEnSecurite(): mixed
    {
        try {
            return function_exists('current_utilisateur') ? current_utilisateur() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Idem pour current_tenant_id().
     */
    protected function recupererTenantIdEnSecurite(): ?int
    {
        try {
            return function_exists('current_tenant_id') ? current_tenant_id() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Transforme le tableau de champs modifiés en texte lisible, du type :
     *   "statutEmploye: attente_prise_poste → archive, telephone: ... → ..."
     */
    protected function formaterChangements(array $changements): string
    {
        $lignes = [];

        foreach ($changements as $champ => $nouvelleValeur) {
            if (in_array($champ, $this->loggableChampsIgnores, true)) {
                continue;
            }

            $ancienneValeur = $this->getOriginal($champ);

            // Rien d'utile à afficher si la valeur n'a en fait pas changé
            // (peut arriver avec certains casts).
            if ($ancienneValeur === $nouvelleValeur) {
                continue;
            }

            $lignes[] = "{$champ}: "
                . $this->formaterValeurLog($ancienneValeur)
                . ' → '
                . $this->formaterValeurLog($nouvelleValeur);
        }

        return implode(', ', $lignes);
    }

    protected function formaterValeurLog(mixed $valeur): string
    {
        if (is_null($valeur)) {
            return 'vide';
        }

        if (is_bool($valeur)) {
            return $valeur ? 'oui' : 'non';
        }

        if ($valeur instanceof \DateTimeInterface) {
            return $valeur->format('d/m/Y');
        }

        return (string) $valeur;
    }
}
