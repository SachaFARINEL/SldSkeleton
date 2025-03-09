<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SOLEDIS
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

namespace Soledis\SldSkeleton\Init;

use Soledis\SldSkeleton\Bridge\NamespaceChanger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class InitCommand extends Command
{
    protected static $defaultName = 'sld:skeleton:init';
    private SymfonyStyle $io;

    protected function configure(): void
    {
        $this
            ->setDescription('Initialize the module')
            ->setHelp('This command allows you to initialize the Soledis Skeleton module.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->title('Initializing the Module');
        $this->io->text('Requiring the "soledis/sldskeletonbase" package via Composer...');

        try {
            $this->fetchSkeletonBase();
            $this->io->success('The "soledis/sldskeletonbase" package has been successfully fetched.');

            $this->installCoreBundle();
        } catch (ProcessFailedException $exception) {
            $this->io->error('An error occurred during initialization:');
            $this->io->error($exception->getMessage());
            return Command::FAILURE;
        } catch (\Exception $exception) {
            $this->io->error('An error occurred while copying files:');
            $this->io->error($exception->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function fetchSkeletonBase(): void
    {
        $process = new Process([
            'composer',
            'require',
            'soledis/sldskeletonbase',
            'dev-main'
        ]);
        $process->mustRun();
    }

    private function installCoreBundle(): void
    {
        try {
            $vendorPath = __DIR__ . '/../../vendor/soledis/sldskeletonbase/src';
            $oldNamespace = 'Soledis\\Sldskeletonbase';
            $newNamespace = 'Soledis\\SldSkeleton';

            $namespaceChanger = new NamespaceChanger($vendorPath, $oldNamespace, $newNamespace);
            $namespaceChanger->changeNamespaces();
            echo "Modification des namespaces terminée avec succès.\n";
        } catch (\Exception $e) {
            echo "Erreur : " . $e->getMessage() . "\n";
        }
    }
}


