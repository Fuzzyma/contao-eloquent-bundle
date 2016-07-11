<?php
/**
 * @author: Ulrich-Matthias Schäfer
 * @creation: 07.07.16 12:02
 * @package: ContaoEloquentBundle
 */

namespace Fuzzyma\Contao\EloquentBundle\Command;

use Contao\CoreBundle\Command\AbstractLockedCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

class CreateDCAForPivotCommand extends AbstractLockedCommand
{

    private $parameters = [];

    protected function configure()
    {
        $this
            ->setName('contao:make:pivot')
            ->setDescription('Creates a dca file for creation of eloquent pivot tables.')
            ->setDefinition(array(
                new InputOption('models', 'm', InputOption::VALUE_REQUIRED, 'The models you want to create the pivot table for'),
                new InputOption('table', 't', InputOption::VALUE_OPTIONAL, 'The name of the table being created (tl_model1_model2)'),
                new InputOption('base', 'b', InputOption::VALUE_OPTIONAL, 'The relative path to your contao bundle', './src')
            ));
    }

    public function getQuestion($question, $default, $sep = ':')
    {
        return $default ? sprintf('<info>%s</info> [<comment>%s</comment>]%s ', $question, $default, $sep) : sprintf('<info>%s</info>%s ', $question, $sep);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getHelper('question');

        $question = new Question($this->getQuestion('Enter a comma separated list of the models you want to create the pivot table for', $input->getOption('models')), $input->getOption('models'));
        $question->setValidator(function ($answer) {
            $answer = array_map(function ($el) {
                return trim($el);
            }, explode(',', $answer));
            if (2 != count($answer)) {
                throw new \RuntimeException(
                    'This Option requires exactly 2 models'
                );
            }
            return $answer;
        });
        $question->setMaxAttempts(3);
        $models = $questionHelper->ask($input, $output, $question);

        $table = $input->getOption('table');

        if (!$table) {
            $table = 'tl_' . strtolower($models[0]) . '_' . strtolower($models[1]);
        }

        $question = new Question($this->getQuestion('Enter the name of the table being created', $table), $table);
        $question->setValidator(function ($answer) {
            if ('tl_' !== substr($answer, 0, 3)) {
                throw new \RuntimeException(
                    'The name of the table should be prefixed with \'tl_\''
                );
            }
            return $answer;
        });
        $question->setMaxAttempts(3);

        $table = $questionHelper->ask($input, $output, $question);

        $question = new Question($this->getQuestion('Enter the relative path to your contao bundle', './src'), './src');
        $base = $questionHelper->ask($input, $output, $question);

        $this->parameters = [
            'models' => $models,
            'table' => $table,
            'base' => $base
        ];
    }

    protected function executeLocked(InputInterface $input, OutputInterface $output)
    {

        if (!isset($this->parameters['models']) && !$input->getOption('models')) {
            throw new \RuntimeException(
                'You have to pass at least the models option (--models=model1,model2)'
            );
        }

        $models = isset($this->parameters['models']) ? $this->parameters['models'] : array_map(function ($el) {
            return trim($el);
        }, explode(',', $input->getOption('models')));
        $table = isset($this->parameters['table']) ? $this->parameters['table'] : $input->getOption('table') ? : 'tl_' . strtolower($models[0]) . '_' . strtolower($models[1]);
        $base = isset($this->parameters['base']) ? $this->parameters['base'] : $input->getOption('base') ? : './src';

        $fs = new Filesystem();
        $path = $this->getContainer()->get('kernel')->getRootDir() .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . $base .
            DIRECTORY_SEPARATOR . 'Resources' .
            DIRECTORY_SEPARATOR . 'contao' .
            DIRECTORY_SEPARATOR . 'dca' .
            DIRECTORY_SEPARATOR . $table . '.php';

        $models = array_map(function ($el) {
            return strtolower($el);
        }, $models);

        $fs->dumpFile($path, $this->getDCAFor(
            $table,
            $models
        ));

        $output->writeln('<info>Success: ' . $table . '.php was created</info>');

    }

    private function getDCAFor($dca, $models)
    {

        $dump = <<<EOT
<?php

/**
 * Table $dca
 */
\$GLOBALS['TL_DCA']['$dca'] = array
(

	// Config
	'config' => array
	(
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment"
		),
        '${models[0]}_id' => array(
            'sql' => "int(10) unsigned NULL"
        ),
        '${models[1]}_id' => array(
            'sql' => "int(10) unsigned NULL"
        )
	)
);

EOT;

        return $dump;
    }

}