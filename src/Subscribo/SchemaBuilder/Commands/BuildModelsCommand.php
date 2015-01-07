<?php namespace Subscribo\SchemaBuilder\Commands;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Subscribo\Config;
use Fuel\Core\Arr;

use App;
use View;

class BuildModelsCommand extends BuildCommandAbstract {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'build:models';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This custom command builds models';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $fileName = $this->argument('file');
        $this->info('Building models starting. Using file: '. $fileName);
        $this->info('Environment: '. App::environment());
        Config::setForPackage('schemabuilder', 'parsed_schema', array());
        Config::loadFileForPackage('schemabuilder', $fileName, 'parsed_schema', true, null);
        $input = Config::getForPackage('schemabuilder', 'parsed_schema');
        $modelFields = $input['model_fields'];
        $modelOptions = $input['model_options'];

        $this->_buildModels($modelFields, $modelOptions, self::MODELS_DIR, self::PACKAGES_CONFIG_DIR.'modelbase/');

        $this->info('Building models finished.');
    }

    private function _buildModels($modelFields, $modelOptions, $basePath, $configPath)
    {
        $modelsForApiConfiguration = array();
        foreach ($modelFields as $tableName => $fields)
        {
            $options = Arr::get($modelOptions, $tableName, array());
            $modelName = $this->_modelNameFromTable($tableName);
            $data = array(
                'fields' => $fields,
                'options' => $options,
                'modelName' => $modelName,
            );
            if ( ! empty($options['generate']['model']['draft']))
            {
                $draftFilePath = $basePath.$modelName.'.php';
                $draftContent = View::make('schemabuilder::commands.build.model_draft', $data);
                $this->_createFile($draftFilePath, $draftContent, $options['generate']['model']['draft']);
            }
            if ( ! empty($options['generate']['model']['base']))
            {
                $baseFilePath = $basePath.'base/'.$modelName.'.php';
                $baseContent = View::make('schemabuilder::commands.build.model_base', $data);
                $this->_createFile($baseFilePath, $baseContent, $options['generate']['model']['base']);
            }
            if ( ! empty($options['generate']['api']))
            {
                $modelsForApiConfiguration[$options['api_stub']] = array(
                    'model_full_name' => $options['model_full_name']
                );
            }
        }
        $apiConfigContent = View::make('schemabuilder::commands.build.api_config', array('apiConfiguration' => array(
            'models' => $modelsForApiConfiguration,
        )));
        $apiConfigFilePath = $configPath.'api.php';
        $this->_createFile($apiConfigFilePath, $apiConfigContent, 'overwrite');
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('file', InputArgument::OPTIONAL, 'File used for schema.', self::SCHEMA_DIR.'parsed_schema.yml'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('stdin', null, InputOption::VALUE_NONE, 'Use stdin instead of file (Not implemented).', null),
		);
	}

}
