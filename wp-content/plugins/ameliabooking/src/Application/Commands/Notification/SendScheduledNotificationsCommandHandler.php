<?php

namespace AmeliaBooking\Application\Commands\Notification;

use AmeliaBooking\Application\Commands\CommandHandler;
use AmeliaBooking\Application\Commands\CommandResult;
use AmeliaBooking\Application\Services\Notification\EmailNotificationService;
use AmeliaBooking\Application\Services\Notification\SMSNotificationService;
use AmeliaBooking\Domain\Entity\Entities;
use AmeliaBooking\Domain\Services\Settings\SettingsService;

/**
 * Class SendScheduledNotificationsCommandHandler
 *
 * @package AmeliaBooking\Application\Commands\Notification
 */
class SendScheduledNotificationsCommandHandler extends CommandHandler
{
    /**
     * @return CommandResult
     * @throws \AmeliaBooking\Domain\Common\Exceptions\InvalidArgumentException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\NotFoundException
     * @throws \AmeliaBooking\Infrastructure\Common\Exceptions\QueryExecutionException
     * @throws \Interop\Container\Exception\ContainerException
     * @throws \Exception
     */
    public function handle()
    {
        $result = new CommandResult();

        /** @var EmailNotificationService $notificationService */
        $notificationService = $this->getContainer()->get('application.emailNotification.service');
        /** @var SMSNotificationService $smsNotificationService */
        $smsNotificationService = $this->getContainer()->get('application.smsNotification.service');
        /** @var SettingsService $settingsService */
        $settingsService = $this->container->get('domain.settings.service');

        $notificationService->sendNextDayReminderNotifications(Entities::APPOINTMENT);
        $notificationService->sendNextDayReminderNotifications(Entities::EVENT);
        $notificationService->sendFollowUpNotifications(Entities::APPOINTMENT);
        $notificationService->sendFollowUpNotifications(Entities::EVENT);
        $notificationService->sendBirthdayGreetingNotifications();

        if ($settingsService->getSetting('notifications', 'smsSignedIn') === true) {
            $smsNotificationService->sendNextDayReminderNotifications(Entities::APPOINTMENT);
            $smsNotificationService->sendNextDayReminderNotifications(Entities::EVENT);
            $smsNotificationService->sendFollowUpNotifications(Entities::APPOINTMENT);
            $smsNotificationService->sendFollowUpNotifications(Entities::EVENT);
            $smsNotificationService->sendBirthdayGreetingNotifications();
        }

        $result->setResult(CommandResult::RESULT_SUCCESS);
        $result->setMessage('Scheduled email notifications successfully sent');

        return $result;
    }
}
