<?php

namespace Fourum\Support;

use Carbon\Carbon;
use Fourum\Notification\NotifierInterface;
use Fourum\Notification\NotifiableInterface;

abstract class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @param string $class
     */
    protected function setupNotifications($class)
    {
        $notificationFactory = $this->app->make('Fourum\Notification\NotificationFactory');

        $notificationFactory->addType($class::TYPE, function (
            NotifierInterface $notifier,
            NotifiableInterface $notifiable,
            $read,
            Carbon $timestamp
        ) use ($class) {
            return new $class($notifier, $notifiable, $read, $timestamp);
        });
    }

    /**
     * @param string $name
     * @param string $foreignKey
     * @param string $class
     */
    protected function setupRepository($name, $foreignKey, $class)
    {
        $repoFactory = $this->app->make('Fourum\Repository\RepositoryFactory');
        $repoRegistry = $this->app->make('Fourum\Repository\RepositoryRegistry');

        $repoFactory->addForeignKey($foreignKey, $class);
        $repoRegistry->add($name, $class);
    }

    /**
     * @param string $path
     */
    protected function setupSettings($path)
    {
        $fileRepo = $this->app->make('Fourum\Setting\Filesystem\SettingRepository');
        $fileRepo->addPath($path);
    }
}
