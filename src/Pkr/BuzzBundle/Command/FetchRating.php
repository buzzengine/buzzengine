<?php

namespace Pkr\BuzzBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchRating extends Command
{
    protected function configure()
    {
        $this->setName('buzz:fetchRating')
             ->setDescription('Fetch rating scores via command line');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $ratingService = $container->get('pkr_buzz.service.rating');

        try
        {
            $ratingService->updateRating();
        }
        catch (\Exception $e)
        {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }

        return 0;
    }
}
