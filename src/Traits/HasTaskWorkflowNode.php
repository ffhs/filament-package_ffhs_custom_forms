<?php

namespace Ffhs\FilamentPackageFfhsCustomForms\Traits;

use Ffhs\FfhsTasks\Models\Task;
use Ffhs\FfhsWorkflows\Models\WorkflowNodeInstance;

trait HasTaskWorkflowNode
{
    public function getNodeInstance(Task $task): ?WorkflowNodeInstance
    {
        return WorkflowNodeInstance::query()->whereHas('tasks', function ($query) use ($task) {
            $query->where(Task::getConfigTable() . '.id', $task->id);
        })->first();
    }

}
