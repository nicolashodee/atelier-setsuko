<?php

namespace AmeliaBooking\Application\Commands\Settings;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Common\Exceptions\AccessDeniedException;
use AmeliaBooking\Application\Services\Location\CurrentLocation;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Services\Settings\SettingsService;
use AmeliaBooking\Infrastructure\Services\Frontend\LessParserService;
use AmeliaBooking\Infrastructure\WP\Integrations\WooCommerce\WooCommerceService;
use Exception;
use Interop\Container\Exception\ContainerException;
use Less_Exception_Parser;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class UpdateSettingsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Settings
 */
class UpdateSettingsCommandHandler extends CommandHandler
{
    /**
     * @param UpdateSettingsCommand $command
     *
     * @return CommandResult
     * @throws AccessDeniedException
     * @throws Less_Exception_Parser
     * @throws ContainerValueNotFoundException
     * @throws ContainerException
     * @throws Exception
     */
    public function handle(UpdateSettingsCommand $command)
    {
        $result = new CommandResult();

        if (!$this->getContainer()->getPermissionsService()->currentUserCanWrite(Entities::SETTINGS)) {
            throw new AccessDeniedException('You are not allowed to write settings.');
        }

        /** @var SettingsService $settingsService */
        $settingsService = $this->getContainer()->get('domain.settings.service');

        /** @var CurrentLocation $locationService */
        $locationService = $this->getContainer()->get('application.currentLocation.service');

        /** @var LessParserService $lessParserService */
        $lessParserService = $this->getContainer()->get('infrastructure.frontend.lessParser.service');

        $settingsFields = $command->getFields();

        if ($command->getField('customization')) {
            $customizationData = $command->getField('customization');

            $hash = $lessParserService->compileAndSave(
                [
                    'color-accent'      => $customizationData['primaryColor'],
                    'color-gradient1'   => $customizationData['primaryGradient1'],
                    'color-gradient2'   => $customizationData['primaryGradient2'],
                    'color-text-prime'  => $customizationData['textColor'],
                    'color-text-second' => $customizationData['textColor'],
                    'color-white'       => $customizationData['textColorOnBackground'],
                    'font'              => $customizationData['font']
                ]
            );

            $settingsFields['customization']['hash'] = $hash;

            $settingsFields['customization']['useGenerated'] = isset($customizationData['useGenerated']) ?
                $customizationData['useGenerated'] : true;
        }

        if (WooCommerceService::isEnabled() &&
            $command->getField('payments') &&
            $command->getField('payments')['wc']['enabled']
        ) {
            $settingsFields['payments']['wc']['productId'] = WooCommerceService::getIdForExistingOrNewProduct(
                $settingsService->getCategorySettings('payments')['wc']['productId']
            );
        }

        if ($command->getField('useWindowVueInAmelia') !== null) {
            $generalSettings = $settingsService->getCategorySettings('general');

            $settingsFields['general'] = $generalSettings;

            $settingsFields['general']['useWindowVueInAmelia'] = $command->getField('useWindowVueInAmelia');

            unset($settingsFields['useWindowVueInAmelia']);
        }

        if ($command->getField('sendAllCF') !== null) {
            $notificationsSettings = $settingsService->getCategorySettings('notifications');

            $settingsFields['notifications'] = $notificationsSettings;

            $settingsFields['notifications']['sendAllCF'] = $command->getField('sendAllCF');

            unset($settingsFields['sendAllCF']);
        }

        $settingsFields['activation'] = array_merge(
            $settingsService->getCategorySettings('activation'),
            isset($settingsFields['activation']['deleteTables']) ? [
                'deleteTables' => $settingsFields['activation']['deleteTables']
            ] : []
        );

        if ($command->getField('usedLanguages') !== null) {
            $generalSettings = $settingsService->getCategorySettings('general');

            $settingsFields['general'] = $generalSettings;

            $settingsFields['general']['usedLanguages'] = $command->getField('usedLanguages');

            unset($settingsFields['usedLanguages']);
        }

        $settingsService->setAllSettings($settingsFields);

        $settings = $settingsService->getAllSettingsCategorized();
        $settings['general']['phoneDefaultCountryCode'] = $settings['general']['phoneDefaultCountryCode'] === 'auto' ?
            $locationService->getCurrentLocationCountryIso() : $settings['general']['phoneDefaultCountryCode'];

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Successfully updated settings.');
        $result->setData(
            [
                'settings' => $settings
            ]
        );

        return $result;
    }
}
