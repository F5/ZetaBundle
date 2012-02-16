<?php
namespace F5\Bundle\ZetaBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Doctrine\Bundle\DoctrineBundle\Mapping\DisconnectedMetadataFactory;

class ReIndexCommand extends ContainerAwareCommand
{
    protected $quiet = false;
    protected $output;

    public function configure()
    {
        $this->setName('f5:zeta:re-index')
            ->setDescription('Reindex all the entities')
            ->setDefinition(array(
                new InputArgument('name', InputArgument::REQUIRED, 'Entity to reindex'),
        ));
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $manager = new DisconnectedMetadataFactory($doctrine);

        try {
            $bundle = $this->getApplication()->getKernel()->getBundle($input->getArgument('name'));

            $output->writeln(sprintf('Reindexing entities for bundle "<info>%s</info>"', $bundle->getName()));
            $metadata = $manager->getBundleMetadata($bundle);
        } catch (\InvalidArgumentException $e) {
            $name = strtr($input->getArgument('name'), '/', '\\');

            if (false !== $pos = strpos($name, ':')) {
                $name = $doctrine->getEntityNamespace(substr($name, 0, $pos)).'\\'.substr($name, $pos + 1);
            }

            if (class_exists($name)) {
                $output->writeln(sprintf('Reindexing entity "<info>%s</info>"', $name));
                $metadata = $manager->getClassMetadata($name);
            } else {
                $output->writeln(sprintf('Reindexing entities for namespace "<info>%s</info>"', $name));
                $metadata = $manager->getNamespaceMetadata($name);
            }
        }

        $this->quiet = $input->getOption('quiet');
        $this->output = $output;

        $session = $this->getContainer()->get('zeta.search.session');

        foreach ($metadata->getMetadata() as $m) {
            $m->initializeReflection(new \Doctrine\Common\Persistence\Mapping\RuntimeReflectionService);

            if(!in_array('ezcBasePersistable',$m->getReflectionClass()->getInterfaceNames())) continue;
            
            // Delete all the entities!
            $this->log("Deleting all the entities");
            $session->beginTransaction();
            $query = $session->createDeleteQuery( $m->name );
            $query->where( '*:*');
            $session->delete( $query );
            $session->commit();

            // Index all the entities!
            $session->beginTransaction();
            foreach($doctrine->getRepository($m->name)->findAll() as $entity){
                $this->log("Reindexing " . (method_exists($entity,'getName')?$entity->getName()." - ":""). $entity->getId());

                $session->index($entity);
            }

            $session->commit();
        }
        $this->log('Done.');
    }

    /**
     * Write a message to the output
     *
     * @param string $message
     * @return void
     */
    protected function log($message)
    {
        if ($this->quiet === false) {
            $this->output->writeln($message);
        }
    }
}
