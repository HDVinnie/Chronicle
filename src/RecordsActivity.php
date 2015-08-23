<?php

namespace Kenarkose\Chronicle;


use ReflectionClass;

trait RecordsActivity {

    /**
     * Registers the event listeners
     */
    protected static function bootRecordsActivity()
    {
        foreach (static::getModelEvents() as $event)
        {
            static::$event(function ($model) use ($event)
            {
                $model->recordActivity($event);
            });
        }
    }

    /**
     * Records an activity through chronicle
     *
     * @param string $event
     * @return Activity
     */
    public function recordActivity($event)
    {
        return chronicle()->record(
            $this,
            $this->getActivityName($event),
            $this->getUserId()
        );
    }

    /**
     * Prepares the activity name
     *
     * @param string $action
     * @return string
     */
    protected function getActivityName($action)
    {
        $name = strtolower((new ReflectionClass($this))->getShortName());

        return $action . '_' . $name;
    }

    /**
     * Returns the user that is associated with the model
     *
     * @return int|null
     */
    protected function getUserId()
    {
        if (property_exists($this, 'userKey'))
        {
            $userKey = $this->userKey;

            return $this->$userKey;
        }

        if ( ! is_null($this->user_id))
        {
            return $this->user_id;
        }

        return null;
    }

    /**
     * Returns the model events to record activity for
     *
     * @return array
     */
    protected static function getModelEvents()
    {
        if (isset(static::$recordEvents))
        {
            return static::$recordEvents;
        }

        return [
            'created', 'deleted', 'updated'
        ];
    }

}