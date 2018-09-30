<?php

namespace TYPO3\CMS\Typo3DbLegacy\Updates;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Install\Updates\AbstractDownloadExtensionUpdate;
use TYPO3\CMS\Install\Updates\Confirmation;
use TYPO3\CMS\Install\Updates\ExtensionModel;

/**
 * Installs and downloads EXT:adodb and EXT:dbal
 */
class DbalAndAdodbExtractionUpdate extends AbstractDownloadExtensionUpdate
{
    /**
     * @var \TYPO3\CMS\Install\Updates\Confirmation
     */
    protected $confirmation;

    /**
     * @var ExtensionModel
     */
    protected $adodb;

    /**
     * @var ExtensionModel
     */
    protected $dbal;

    public function __construct()
    {
        $this->adodb = new ExtensionModel(
            'adodb',
            'ADOdb',
            '8.4.0',
            'friendsoftypo3/adodb',
            'Adds ADOdb to TYPO3'
        );

        $this->dbal = new ExtensionModel(
            'dbal',
            'dbal',
            '8.4.0',
            'friendsoftypo3/dbal',
            'Adds old database abstraction layer to TYPO3'
        );

        $this->confirmation = new Confirmation(
            'Are you sure?',
            'You should install EXT:adodb and EXT:dbal only if you really need it.'
            . ' This update wizard cannot check if the extension was installed before the update.'
            . ' Are you really sure, you want to install these two extensions?'
            . ' They are only needed if this instance connects to a database server that is NOT MySQL'
            . ' and if an active extension uses extension typo3db_legacy and a table mapping for EXT:dbal'
            . ' is configured.'
            . ' Loading these two extensions is a rather seldom exceptions, the vast majority of'
            . ' instances should say "no" here.',
            false
        );
    }


    /**
     * Return a confirmation message instance
     *
     * @return \TYPO3\CMS\Install\Updates\Confirmation
     */
    public function getConfirmation(): Confirmation
    {
        return $this->confirmation;
    }


    /**
     * Return the identifier for this wizard
     * This should be the same string as used in the ext_localconf class registration
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'dbalAndAdodbExtraction';
    }

    /**
     * Return the speaking name of this wizard
     *
     * @return string
     */
    public function getTitle(): string
    {
        return '[Optional] Install extensions "dbal" and "adodb" from TER.';
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'The extensions "dbal" and "adodb" have been extracted to'
               . ' the TYPO3 Extension Repository. This update downloads the TYPO3 Extension from the TER'
               . ' if the two extensions are still needed.';
    }

    /**
     * Is an update necessary?
     * Is used to determine whether a wizard needs to be run.
     *
     * @return bool
     */
    public function updateNecessary(): bool
    {
        return !ExtensionManagementUtility::isLoaded($this->dbal->getKey()) ||
               !ExtensionManagementUtility::isLoaded($this->adodb->getKey());
    }


    /**
     * Execute the update
     * Called when a wizard reports that an update is necessary
     *
     * @return bool
     */
    public function executeUpdate(): bool
    {
        return $this->installExtension($this->dbal) && $this->installExtension($this->adodb);
    }

    /**
     * Returns an array of class names of Prerequisite classes
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [];
    }
}
