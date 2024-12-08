<?php

namespace App\Filament\Cores\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Js;

class CopyAction extends Action
{
    protected Closure|string|null $copyable = null;

    public static function getDefaultName(): ?string
    {
        return 'copy';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->icon('heroicon-o-clipboard');

        $this->action(function ($livewire, $record) {
            Notification::make()
                ->title('Barcode berhasil dibuat')
                ->body('Silahkan paste barcode di tab baru')
                ->success()
                ->send();
        });
    }

    /**
     * @throws \JsonException
     */
    public function copyable(Closure|string|null $copyable): self
    {
        $this->copyable = $copyable;

        $this->extraAttributes([
            'x-data' => '',
            'x-on:click' => new HtmlString(
                'window.navigator.clipboard.writeText('.\Illuminate\Support\Js::from($this->getCopyable()).');'
                .(($title = $this->getSuccessNotificationTitle()) ? ' $tooltip('.\Illuminate\Support\Js::from($title).');' : '')
            ),
        ]);

        return $this;
    }

    public function getCopyable(): ?string
    {
        return $this->evaluate($this->copyable);
    }
}
