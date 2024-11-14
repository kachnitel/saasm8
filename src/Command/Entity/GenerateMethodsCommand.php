<?php

namespace App\Command\Entity;

use App\Entity\Traits\GetterSetterCall;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Form\Extension\Validator\ViolationMapper\MappingRule;

#[AsCommand(
    name: 'app:entity:generate-methods',
    description: 'Add a short description for your command',
)]
class GenerateMethodsCommand extends Command
{
    use GetterSetterCall;

    const ACTIONS = [
        'get' => '<info>%retval</info> <comment>%method</comment>()',
        'set' => '<info>self</info> <comment>%method</comment>(<info>%argType</info> $%property)',
        'add' => '<info>self</info> <comment>%method</comment>(<info>%argType</info> $%property)',
        'remove' => '<info>self</info> <comment>%method</comment>(<info>%argType</info> $%property)',
    ];

    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::OPTIONAL, 'Entity class name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $entity = $input->getArgument('entity');

        if (!$entity) {
            // how to get all entities?
            $entities = $this->entityManager->getConfiguration()->getMetadataDriverImpl()
                ->getAllClassNames();
            $options = array_combine(
                str_replace('App\\Entity\\', '', $entities),
                $entities
            );

            $entity = $io->choice('Select an entity', $options);
        }

        $metadata = $this->entityManager->getClassMetadata($options[$entity]);

        /** @see App\Entity\Traits\GetterSetterCall */
        $methods = [
            'getters' => [],
            'setters' => [],
            'ar' => []
        ];

        foreach ($metadata->fieldMappings as $field => $mapping) {
            $type = $this->convertInternalType($mapping['type']);

            // * @method int getId()
            $methods['getters'][] = $this->printLine($type, '', 'get', $field);

            if ($field === 'id') {
                continue;
            }

            // * @method self setStartTime(\DateTimeInterface $startTime)
            $methods['setters'][] = $this->printLine('self', $type, 'set', $field);
        }

        /** @var Mapping\OneToOneAssociationMapping|Mapping\OneToManyAssociationMapping|Mapping\ManyToManyAssociationMapping|Mapping\ManyToOneAssociationMapping $mapping */
        foreach ($metadata->associationMappings as $field => $mapping) {
            $type = $this->stripEntityNamespace($mapping['targetEntity']);
            $isCollection = $mapping instanceof Mapping\ManyToManyAssociationMapping
                || $mapping instanceof Mapping\OneToManyAssociationMapping;

            if ($isCollection) {
                $returnType = 'Collection<int, ' . $type . '>';
                $methods['getters'][] = $this->printLine($returnType, '', 'get', $field);

                $singular = $this->pluralToSingular($field);

                $methods['ar'][] = $this->printLine('self', $type, 'add', $singular);
                $methods['ar'][] = $this->printLine('self', $type, 'remove', $singular);
            } else {
                $methods['getters'][] = $this->printLine($type, '', 'get', $field);
                $methods['setters'][] = $this->printLine('self', $type, 'set', $field);
            }
        }

        // getters, setters, adders+removers
        $methodsSorted = array_merge($methods['getters'], $methods['setters'], $methods['ar']);
        $result = '/**'
            . PHP_EOL . implode(PHP_EOL, $methodsSorted)
            . PHP_EOL . ' */';

        $io->writeln($result);

        if ($io->ask('Do you want to write this to the entity class?', 'yes') === 'yes') {
            $this->writeToFile($options[$entity], $result);
        }

        return Command::SUCCESS;
    }

    private function printLine(
        string $returnType,
        string $argType,
        string $action,
        string $name,
    ): string {
        // use strtr to replace placeholders
        $line = strtr(self::ACTIONS[$action], [
            '%retval' => $returnType,
            '%property' => $name,
            '%method' => $action . ucfirst($name),
            '%argType' => $argType
        ]);

        return ' * @method ' . $line;
    }

    private function pluralToSingular(string $plural): string
    {
        if (substr($plural, -3) === 'ies') {
            return substr($plural, 0, -3) . 'y';
        }

        if (substr($plural, -1) === 's') {
            return substr($plural, 0, -1);
        }

        throw new \InvalidArgumentException('Unknown plural form');
    }

    private function stripEntityNamespace(string $entity): string
    {
        return str_replace('App\\Entity\\', '', $entity);
    }

    /**
     * Convert internal types to PHP types
     * TODO: isn't there a better way to get this information?
     */
    private function convertInternalType(string $type): string
    {
        $types = [
            'datetime' => '\DateTimeInterface',
            'datetimetz' => '\DateTimeInterface',
            'date' => '\DateTimeInterface',
            'time' => '\DateTimeInterface',
            'text' => 'string',
            'string' => 'string',
            'integer' => 'int',
            'smallint' => 'int',
            'bigint' => 'int',
            'decimal' => 'float',
            'float' => 'float',
            'boolean' => 'bool',
            'array' => 'array',
            'simple_array' => 'array',
            'json' => 'array',
            'object' => 'object',
            'blob' => 'string',
            'guid' => 'string',
            'binary' => 'string',
            'varbinary' => 'string',
            'char' => 'string',
            'enum' => 'string',
            'set' => 'string',
        ];

        return $types[$type] ?? $type;
    }

    private function writeToFile(string $entity, string $content): void
    {
        // TODO:
    }
}
