<?php

namespace Game\Domain\Service;

use Game\Domain\Entity\Game;

final class CheckIfSomeoneWinsService
{
    private const NEEDED_COUNT_OF_STEPS = 5;

    private const OWNER_MARK = 1;

    private const COMPETITOR_MARK = 0;

    public function checkWinner(Game $game): void
    {
        $steps = $game->getSteps();
        if (count($steps) >= self::NEEDED_COUNT_OF_STEPS) {
            $matrix = $this->getDefaultMatrix();
            foreach ($game->getOwner()->getSteps() as $step) {
                $matrix[$step->getX()->getValue()][$step->getY()->getValue()] = self::OWNER_MARK;
            }

            if ($this->algo($matrix, self::OWNER_MARK)) {
                $game->setWinner($game->getOwner());
                return;
            }

            foreach ($game->getCompetitor()->getSteps() as $step) {
                $matrix[$step->getX()->getValue()][$step->getY()->getValue()] = self::COMPETITOR_MARK;
            }

            if ($this->algo($matrix, self::COMPETITOR_MARK)) {
                $game->setWinner($game->getCompetitor());
            }
        }
    }

    private function algo(array $matrix, int $mark): bool
    {
        $winRow = array_fill(0, 3, $mark);
        for ($i = 0; $i < 3; $i++) {
            if ($matrix[$i] === $winRow) {
                return true;
            }
        }
        for ($i = 0; $i < 3; $i++) {
            if ([$matrix[0][$i], $matrix[1][$i], $matrix[2][$i]] === $winRow) {
                return true;
            }
        }
        return [$matrix[0][0], $matrix[1][1], $matrix[2][2]] === $winRow ||
            [$matrix[2][0], $matrix[1][1], $matrix[0][2]] === $winRow;
    }

    private function getDefaultMatrix(): array
    {
        return array_fill(0, 3, array_fill(0, 3, null));
    }
}
