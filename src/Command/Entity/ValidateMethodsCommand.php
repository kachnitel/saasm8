<?php

namespace App\Command\Entity;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Validate all methods using the generator and comparing results
 * - only compare @method lines at the top of the class
 * - order of methods does not matter
 * - ignore any other comments, /** etc
 * - replace multiple spaces with a single space in the method signature.
 */
#[AsCommand(
    name: 'app:entity:validate-methods',
    description: 'Validate getter, setter, adder and remover methods comment docblock for all entities are up to date'
)]
class ValidateMethodsCommand extends GenerateMethodsCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityName = $input->getArgument('entity');

        $entities = $entityName
            ? ['App\\Entity\\' . $entityName]
            : $this->entityManager->getConfiguration()->getMetadataDriverImpl()
                ->getAllClassNames();

        $errors = [];
        foreach ($entities as $entity) {
            $metadata = $this->entityManager->getClassMetadata($entity);
            $methods = $this->generateMethods($metadata, $this->stripEntityNamespace($entity));

            $file = file_get_contents((new \ReflectionClass($entity))->getFileName());
            $lines = explode("\n", $file);

            $expected = $this->getExpectedMethods($methods);
            $actual = $this->getActualMethods($lines);

            if ($expected !== $actual) {
                $errors[] = $entity;
            }
        }

        if ($errors) {
            $output->writeln('Errors found in the following entities:');
            foreach ($errors as $error) {
                $output->writeln($error);
            }

            return Command::FAILURE;
        }

        $message = $entityName ? $entityName . ' is up to date' : 'All entities are up to date';
        $output->writeln($message);

        return Command::SUCCESS;
    }

    private function getExpectedMethods(array $methods): array
    {
        $expected = [];
        foreach ($methods as $method) {
            // REVIEW:
            $expected[] = $this->stripWhitespace($this->stripHighlighting($method));
        }

        return $expected;
    }

    private function getActualMethods(array $lines): array
    {
        $actual = [];
        foreach ($lines as $line) {
            if (false !== strpos($line, '@method')) {
                $actual[] = $this->stripWhitespace($line);
            }
        }

        return $actual;
    }

    private function stripWhitespace(string $line): string
    {
        return preg_replace('/\s+/', ' ', $line);
    }
}
