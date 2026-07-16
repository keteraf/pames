<?php

namespace App\Console\Commands;

use App\Actions\EquationQuest\GenerateEquationQuestPuzzle;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

#[Signature('equation-quest:generate {--date= : The date to generate for (defaults to today)} {--force : Regenerate even if a puzzle already exists}')]
#[Description('Generate the daily solvable Equation Quest puzzle.')]
class GenerateEquationQuestCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(GenerateEquationQuestPuzzle $generate): int
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();

        $puzzle = $generate->handle($date, (bool) $this->option('force'));

        $this->components->info(sprintf(
            'Equation Quest for %s: reach %d using [%s].',
            $puzzle->puzzle_date->toDateString(),
            $puzzle->target,
            implode(', ', $puzzle->numbers),
        ));

        return self::SUCCESS;
    }
}
