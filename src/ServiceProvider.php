<?php

namespace Fourum\Support;

use Illuminate\Support\Facades\Schema;

abstract class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * @param string $class
     */
    protected function setupNotifications($class)
    {
        $type = constant($class . "::TYPE");
        $typeRepository = $this->app->make('Fourum\Notification\Type\TypeRepositoryInterface');

        if (Schema::hasTable('notification_types')) {
            if (! $typeRepository->hasType($type)) {
                $typeRepository->createAndSave(['name' => $type]);
            }
        }

        $notificationFactory = $this->app->make('Fourum\Notification\NotificationFactory');
        $notificationFactory->addType($type, function ($notifier, $notifiable, $read, $timestamp) use ($class) {
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
