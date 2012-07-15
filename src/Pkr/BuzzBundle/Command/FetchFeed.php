<?php

namespace Pkr\BuzzBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchFeed extends Command
{
    protected function configure()
    {
        $this->setName('buzz:fetchFeed')
             ->setDescription('Fetch feeds via command line')
             ->addArgument('frequency', InputArgument::OPTIONAL, 'the frequency type');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $feedService = $container->get('pkr_buzz.service.feed');

        $frequency = $input->getArgument('frequency');
        try
        {
            if (empty ($frequency))
            {
                $feedService->fetch();
            }
            else
            {
                $feedService->fetch($frequency);
            }
        }
        catch (\Exception $e)
        {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }

        return 0;
    }
}
