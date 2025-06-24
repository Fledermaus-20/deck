<?php

namespace OCA\Deck\Command;

use OCP\IUserManager;
use OCP\IConfig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SetCalendarOptOut extends Command {
    private IUserManager $userManager;
    private IConfig $config;

    public function __construct(IUserManager $userManager, IConfig $config) {
        parent::__construct();
        $this->userManager = $userManager;
        $this->config = $config;
    }

    protected function configure() {
        $this
            ->setName('deck:calendar-optout')
            ->setDescription('Set Deck calendar/tasks integration to opt-out for all users (enabled by default, users can opt-out).')
            ->addOption(
                'on',
                null,
                InputOption::VALUE_NONE,
                'Enable calendar/tasks integration for all users (opt-out, default)'
            )
            ->addOption(
                'off',
                null,
                InputOption::VALUE_NONE,
                'Disable calendar/tasks integration for all users (opt-in)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $enable = $input->getOption('on');
        $disable = $input->getOption('off');
        if ($enable && $disable) {
            $output->writeln('<error>Cannot use --on and --off together.</error>');
            return 1;
        }
        $value = $enable ? 'yes' : '';
        $users = $this->userManager->search("");
        $count = 0;
        foreach ($users as $user) {
            $uid = $user->getUID();
            $this->config->setUserValue($uid, 'deck', 'calendar', $value);
            $output->writeln("Set calendar integration to '" . ($enable ? 'on' : 'off') . "' for user: $uid");
            $count++;
        }
        $output->writeln("Done. Updated $count users.");
        return 0;
    }
} 