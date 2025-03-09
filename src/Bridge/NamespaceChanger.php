<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SOLEDIS.
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SOLEDIS GROUP is strictly forbidden.
 *    ___  ___  _    ___ ___ ___ ___
 *   / __|/ _ \| |  | __|   \_ _/ __|
 *   \__ \ (_) | |__| _|| |) | |\__ \
 *   |___/\___/|____|___|___/___|___/
 *
 * @author    SOLEDIS <prestashop@groupe-soledis.com>
 * @copyright 2025 SOLEDIS
 * @license   All Rights Reserved
 * @developer FARINEL Sacha
 */
declare(strict_types=1);

namespace Soledis\SldSkeleton\Bridge;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

/**
 * Classe permettant de modifier les namespaces d'un projet.
 */
class NamespaceChanger
{
    private string $vendorPath;    // Chemin vers les fichiers cibles.
    private string $oldNamespace; // Namespace à remplacer.
    private string $newNamespace; // Nouveau namespace à appliquer.

    /**
     * NamespaceChanger constructor.
     *
     * @param string $vendorPath    Chemin ciblé dans le répertoire (par exemple: vendor/soledis/sldskeletonbase/src).
     * @param string $oldNamespace  Ancien namespace.
     * @param string $newNamespace  Nouveau namespace.
     */
    public function __construct(string $vendorPath, string $oldNamespace, string $newNamespace)
    {
        if (!is_dir($vendorPath)) {
            throw new \InvalidArgumentException("Le chemin {$vendorPath} n'existe pas ou n'est pas un répertoire valide.");
        }

        $this->vendorPath = $vendorPath;
        $this->oldNamespace = rtrim($oldNamespace, '\\');
        $this->newNamespace = rtrim($newNamespace, '\\');
    }

    /**
     * Lance la modification de tous les namespaces.
     */
    public function changeNamespaces(): void
    {
        // Parcourir récursivement le répertoire pour trouver les fichiers PHP.
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->vendorPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($files as $file) {
            // Vérifier si c'est un fichier PHP.
            if ($file->isFile() && pathinfo($file->getRealPath(), PATHINFO_EXTENSION) === 'php') {
                $this->updateNamespaceInFile($file->getRealPath());
            }
        }
    }

    /**
     * Modifie le namespace dans un fichier donné.
     *
     * @param string $filePath Chemin complet du fichier à modifier.
     */
    private function updateNamespaceInFile(string $filePath): void
    {
        $contents = file_get_contents($filePath);

        if ($contents === false || trim($contents) === '') {
            throw new \RuntimeException("Impossible de lire le fichier : $filePath");
        }

        // Vérifie si le fichier contient au moins l'ancien namespace
        if (str_contains($contents, $this->oldNamespace)) {
            // Remplacement "namespace" en conservant le reste
            $updatedContents = preg_replace(
                "/namespace\s+" . preg_quote($this->oldNamespace, '/') . "(.*);/",
                "namespace {$this->newNamespace}$1;",
                $contents
            );

            // Remplacement "use" en conservant le reste
            $updatedContents = preg_replace(
                "/use\s+" . preg_quote($this->oldNamespace, '/') . "(.*);/",
                "use {$this->newNamespace}$1;",
                $updatedContents
            );

            // Écriture des modifications dans le fichier
            if (file_put_contents($filePath, $updatedContents) === false) {
                throw new \RuntimeException("Erreur lors de l'écriture dans le fichier : $filePath");
            }

            echo "Namespace mis à jour dans : $filePath\n";
        } else {
            // Le fichier ne contient pas de références à l'ancien namespace.
            echo "Aucun changement effectué pour : $filePath\n";
        }
    }
}
