<?php 
App::import('Model');
class InsertIndexShell extends Shell
{
    public function main()
    {
        $models = $this->getModels();
        $this->insertIndexes($models);
    }

    private function getModels()
    {
        return App::objects('model');
    }

    private function insertIndexes($models)
    {
        foreach($models as $model)
        {
            App::import('Model',$model);
            $this->{$model} = new $model();
            $this->{$model}->recursive = -1;
            $this->fieldsToIndex($this->{$model});
        }
    }

    private function fieldsToIndex($modelObj)
    {
        foreach($modelObj->belongsTo as $relacionamento)
        {
            $field = $modelObj->_schema[$relacionamento['foreignKey']];
            if(!empty($field) && !array_key_exists('key',$field))
            {
                if($modelObj->query("ALTER TABLE `{$modelObj->tablePrefix}{$modelObj->table}` ADD INDEX (`{$relacionamento['foreignKey']}`)"))
                {
                    $this->out("Indice {$relacionamento['foreignKey']} adicionado em {$modelObj->table}");
                }
                else
                {
                    $this->out("impossivel adicionar indice {$relacionamento['foreignKey']} em {$modelObj->table}");
                }
            }
        }
    }
}
?>
