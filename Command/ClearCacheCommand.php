<?php
// Sokolov Innokenty, <r2.kenny@gmail.com>

namespace Avalanche\Bundle\ImagineBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ClearCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('avalanche:cache:clear')
            ->setDescription('Clear avalanche picture cache')
            ->addArgument('filter', InputArgument::OPTIONAL, 'Filter name')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Clear all filters')
            ->setHelp(<<<EOF
The <info>avalanche:cache:clear</info> command clears the avalanche picture cache.

Clear one filter by name:
<info>php app/console magicture:cache:clear filter_name</info>

Clear all filters:
<info>php app/console magicture:cache:clear --all</info>
EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container   = $this->getContainer();
        $webRoot     = $container->getParameter('imagine.web_root');
        $cachePrefix = $container->getParameter('imagine.cache_prefix');
        $filters     = $container->getParameter('imagine.filters');

        // clear one
        if ($filterName = $input->getArgument('filter')) {
            if (!isset($filters[$filterName])) {
                throw new \RuntimeException(sprintf(
                    'Could not find filter "%s"', $filterName
                ));
            }
            $this->clearByFilter($webRoot, $cachePrefix, $filterName, $filters[$filterName], $output);
            exit();
        }

        // clear all
        if ($input->getOption('all')) {
            foreach ($filters as $filterName => $options) {
                $this->clearByFilter($webRoot, $cachePrefix, $filterName, $options, $output);
            }
        } else {
            $output->writeln('<error>ATTENTION:</error> This operation clears all cache.');
            $output->writeln('');
            $output->writeln('Please run the operation with --all to execute');
        }
    }

    protected function clearByFilter($webRoot, $cachePrefix, $filterName, $options, OutputInterface $output)
    {
        $dir = isset($options['path'])
            ? $webRoot.'/'.$options['path']
            : $webRoot.'/'.$cachePrefix.'/'.$filterName;

        $output->writeln(sprintf('Clearing the avalanche cache, filter: <info>%s</info>', $filterName));
        $this->getContainer()->get('filesystem')->remove($dir);
    }
}
