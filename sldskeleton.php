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

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once(dirname(__FILE__) . '/vendor/autoload.php');

class SldSkeleton extends Module
{
    public function __construct()
    {
        $this->name = 'sldskeleton';
        $this->author = 'Soledis';
        $this->version = '1.0.0';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('SldSkeleton Module');
        $this->description = $this->l('Module using the Soledis Skeleton base.');
    }
}
