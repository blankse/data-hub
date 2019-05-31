<?php
/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PEL
 */

namespace Pimcore\Bundle\DataHubBundle\Command\GraphQL;

use Elements\Bundle\ProcessManagerBundle\ExecutionTrait;
use Pimcore\Bundle\DataHubBundle\Configuration;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RebuildDefinitionsCommand extends AbstractCommand
{


    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('datahub:graphql:rebuild-definitions')
            ->setDescription("Rebuild GraphQL endpoint definitions")
            ->addOption(
            'definitions',
                null,
                InputOption::VALUE_REQUIRED,
            'Comma separated list of endpoints'
            )
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {

        $included = [];
        if ($input->getOption("definitions")) {
            $included = $input->getOption("definitions");
            $included = explode(',', $included);
        } else {
            $list = Configuration\Dao::getList();
            foreach ($list as $configuration) {
                $endpoint = $configuration->getName();
                $included[]= $endpoint;
            }
        }

        foreach ($included as $endpoint) {

            $config = Configuration::getByName($endpoint);
            if (!$config) {
                $this->output->writeln('<error>Could not find config: ' . $endpoint . '</error>');
                continue;
            }
            $this->output->writeln('Save config: ' . $endpoint);

             $config->save();
        }

        $this->output->writeln('done');
    }
}
