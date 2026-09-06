<?php

namespace Tests\Unit;

use App\Enums\BookingStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BookingStatusTest extends TestCase
{
    public static function transitions(): array
    {
        return [
            'accept' => ['pending', 'accepted', true], 'decline' => ['pending', 'rejected', true], 'start' => ['accepted', 'in_progress', true], 'finish' => ['in_progress', 'completed', true],
            'skip' => ['pending', 'completed', false], 'reopen' => ['completed', 'pending', false], 'reaccept' => ['rejected', 'accepted', false], 'restart cancelled' => ['cancelled', 'in_progress', false],
        ];
    }

    #[DataProvider('transitions')]
    public function test_status_transition_contract(string $from, string $to, bool $allowed): void
    {
        $this->assertSame($allowed, BookingStatus::from($from)->canTransitionTo(BookingStatus::from($to)));
    }
}
