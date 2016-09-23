<?php

namespace app\models;

use Yii;
use app\models\upload\ContentUpload;

/**
 * This is the model class for table "content".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $type_id
 * @property string $data
 * @property int $duration
 * @property string $start_ts
 * @property string $end_ts
 * @property string $add_ts
 * @property bool $enabled
 * @property Flow $flow
 * @property ContentType $type
 */
class Content extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'flow_id', 'type_id'], 'required'],
            [['flow_id', 'type_id', 'duration'], 'integer'],
            [['data'], 'string'],
            [['start_ts', 'end_ts', 'add_ts'], 'safe'],
            [['enabled'], 'boolean'],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 1024],
            [['flow_id'], 'exist', 'skipOnError' => true, 'targetClass' => Flow::className(), 'targetAttribute' => ['flow_id' => 'id']],
            [['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContentType::className(), 'targetAttribute' => ['type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'flow_id' => Yii::t('app', 'Flow'),
            'type_id' => Yii::t('app', 'Type'),
            'data' => Yii::t('app', 'Content'),
            'duration' => Yii::t('app', 'Duration in seconds'),
            'start_ts' => Yii::t('app', 'Start at'),
            'end_ts' => Yii::t('app', 'End on'),
            'add_ts' => Yii::t('app', 'Added at'),
            'enabled' => Yii::t('app', 'Enabled'),
        ];
    }

    public function shouldDeleteFile()
    {
        if ($this->type->kind == ContentType::KINDS['FILE']) {
            return self::find()
                ->joinWith(['type'])
                ->where(['kind' => ContentType::KINDS['FILE']])
                ->andWhere(['data' => $this->data])
                ->count() == 1;
        }

        return false;
    }

    public function getRealFilepath()
    {
        if ($this->type->kind == ContentType::KINDS['FILE']) {
            return ContentUpload::toRealPath($this->data);
        }

        return;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFlow()
    {
        return $this->hasOne(Flow::className(), ['id' => 'flow_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ContentType::className(), ['id' => 'type_id']);
    }
}
