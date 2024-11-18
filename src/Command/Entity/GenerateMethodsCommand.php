<?php

namespace App\Command\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:entity:generate-methods',
    description: 'Generate getter, setter, adder and remover methods comment docblock for an entity',
)]
class GenerateMethodsCommand extends Command
{
    const ACTIONS = [
        'get' => '<info>%retval</info> <comment>%method</comment>()',
        'set' => '<info>self</info> <comment>%method</comment>(<info>%argType</info> $%property)',
        'add' => '<info>self</info> <comment>%method</comment>(<info>%argType</info> $%property)',
        'remove' => '<info>self</info> <comment>%method</comment>(<info>%argType</info> $%property)',
    ];

    public function __construct(protected EntityManagerInterface $entityManager)
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
                $this->stripEntityNamespace($entities),
                $entities
            );

            $entity = $io->choice('Select an entity', $options);
        }

        $metadata = $this->entityManager->getClassMetadata($options[$entity]);

        /** @see App\Entity\Traits\GetterSetterCall */

        $methods = $this->generateMethods($metadata, $entity);

        $result = '/**'
            . PHP_EOL . implode(PHP_EOL, $methods)
            . PHP_EOL . ' */';

        $io->writeln($result);

        if ($io->ask('Do you want to write this to the entity class?', 'yes') === 'yes') {
            $this->writeToFile($entity, $methods);
        }

        return Command::SUCCESS;
    }

    protected function generateMethods(Mapping\ClassMetadata $metadata, string $entity): array
    {
        $methods = [
            'getters' => [],
            'setters' => [],
            'ar' => []
        ];

        foreach ($metadata->fieldMappings as $field => $mapping) {
            $type = $this->convertInternalType($mapping['type']);
            if ($this->isPropertyNullable($entity, $field)) {
                $type .= '|null';
            }

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
                // $nullableType = $mapping['nullable'] ?? false ? $type . '|null' : $type;
                $nullableType = $this->isPropertyNullable($entity, $field) ? $type . '|null' : $type;
                $methods['getters'][] = $this->printLine($nullableType, '', 'get', $field);
                $methods['setters'][] = $this->printLine('self', $nullableType, 'set', $field);
            }
        }

        // Flatten in order - getters, setters, adders+removers
        return array_merge($methods['getters'], $methods['setters'], $methods['ar']);
    }

    protected function stripEntityNamespace(string|array $entity): string|array
    {
        return str_replace('App\\Entity\\', '', $entity);
    }

    protected function stripHighlighting(string $line): string
    {
        return str_replace(['<info>', '</info>', '<comment>', '</comment>'], '', $line);
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


    /**
     * Find if a docblock exists at the top of the class (consider attributes between block and class)
     * If it contains ant @method statements, remove them first
     * Append new methods
     *
     * REVIEW: bit chaotic but it's late!
     */
    private function writeToFile(string $entity, array $methods): void
    {
        $path = 'src/Entity/' . $entity . '.php';
        $file = file_get_contents($path);
        $lines = explode(PHP_EOL, $file);

        // find where class starts - first line starting with 'class $entity' (but may continue after space)
        foreach ($lines as $index => $line) {
            if (str_starts_with($line, 'class ' . $entity)) {
                $classStartIndex = $index;
                break;
            }
        }

        // find if there are any #[Attributes] immediately before the class
        $attributes = [];
        for ($i = $classStartIndex - 1; $i >= 0; $i--) {
            if (str_starts_with($lines[$i], '#[')) {
                $attributes[] = $lines[$i];
            } else {
                break;
            }
        }

        $startIndex = $classStartIndex - count($attributes);

        // remove existing @method lines
        foreach ($lines as $index => $line) {
            if ($index > $classStartIndex) {
                break;
            }

            if (str_starts_with($line, ' * @method')) {
                unset($lines[$index]);
                $startIndex--;
            }
        }

        // if current lines are /** and  */, remove them
        if ($lines[$startIndex - 2] === '/**') {
            // find next line that is not empty
            $i = $startIndex - 1;
            while (empty($lines[$i])) {
                $i++;
            }

            if ($lines[$i] === ' */') {
                unset($lines[$startIndex - 2], $lines[$i]);
                $startIndex -= 2;
            }
        }

        // insert new @method lines before the attributes or class
        array_splice($lines, $startIndex, 0, '/**');
        foreach ($methods as $method) {
            $method = $this->stripHighlighting($method);
            array_splice($lines, ++$startIndex, 0, $method);
        }
        array_splice($lines, ++$startIndex, 0, ' */');

        // write back to file
        file_put_contents($path, implode(PHP_EOL, $lines));
    }

    private function isPropertyNullable(string $entityClass, string $property): bool
    {
        $reflection = new \ReflectionClass('App\\Entity\\' . $entityClass);
        $property = $reflection->getProperty($property);
        return $property->getType()?->allowsNull() ?? false;
    }
}
