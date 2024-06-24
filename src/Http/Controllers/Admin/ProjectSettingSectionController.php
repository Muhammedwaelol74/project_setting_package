<?php

namespace Mabrouk\ProjectSetting\Http\Controllers\Admin;

use Mabrouk\ProjectSetting\Models\ProjectSettingGroup;
use Mabrouk\ProjectSetting\Http\Controllers\Controller;
use Mabrouk\ProjectSetting\Models\ProjectSettingSection;
use Mabrouk\ProjectSetting\Filters\Admin\ProjectSettingSectionFilter;
use Mabrouk\ProjectSetting\Http\Resources\Admin\ProjectSettingSectionResource;
use Mabrouk\ProjectSetting\Http\Requests\Admin\ProjectSettingSectionStoreRequest;
use Mabrouk\ProjectSetting\Http\Requests\Admin\ProjectSettingSectionUpdateRequest;

class ProjectSettingSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Mabrouk\ProjectSetting\Models\ProjectSettingGroup  $project_setting_group
     * @param  \Mabrouk\ProjectSetting\Filters\Admin\ProjectSettingSectionFilter  $filters
     * @return \Illuminate\Http\Response
     */
    public function index(ProjectSettingGroup $project_setting_group, ProjectSettingSectionFilter $filters)
    {
        $paginationLength = pagination_length(ProjectSettingSection::class);
        $projectSettingSections = $project_setting_group->projectSettingSections()->filter($filters)->paginate($paginationLength);
        return ProjectSettingSectionResource::collection($projectSettingSections);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Mabrouk\ProjectSetting\Http\Requests\Admin\ProjectSettingSectionStoreRequest  $request
     * @param  \Mabrouk\ProjectSetting\Models\ProjectSettingGroup  $project_setting_group
     * @return \Illuminate\Http\Response
     */
    public function store(ProjectSettingSectionStoreRequest $request, ProjectSettingGroup $project_setting_group)
    {
        $projectSettingSection = $request->storeProjectSettingSection();
        return response([
            'message' => __('mabrouk/project_settings/project_setting_sections.store'),
            'project_setting_section' => new ProjectSettingSectionResource($projectSettingSection),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Mabrouk\ProjectSetting\Models\ProjectSettingGroup  $project_setting_group
     * @param  \Mabrouk\ProjectSetting\Models\ProjectSettingSection  $project_setting_section
     * @return \Illuminate\Http\Response
     */
    public function show(ProjectSettingGroup $project_setting_group, ProjectSettingSection $project_setting_section)
    {
        return response([
            'project_setting_section' => new ProjectSettingSectionResource($project_setting_section),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Mabrouk\ProjectSetting\Http\Requests\Admin\ProjectSettingSectionUpdateRequest  $request
     * @param  \Mabrouk\ProjectSetting\Models\ProjectSettingGroup  $project_setting_group
     * @param  \Mabrouk\ProjectSetting\Models\ProjectSettingSection  $project_setting_section
     * @return \Illuminate\Http\Response
     */
    public function update(ProjectSettingSectionUpdateRequest $request, ProjectSettingGroup $project_setting_group, ProjectSettingSection $project_setting_section)
    {
        $projectSettingSection = $request->updateProjectSettingSection();
        return response([
            'message' => __('mabrouk/project_settings/project_setting_sections.update'),
            'project_setting_section' => new ProjectSettingSectionResource($projectSettingSection),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Mabrouk\ProjectSetting\Models\ProjectSettingGroup  $project_setting_group
     * @param  \Mabrouk\ProjectSetting\Models\ProjectSettingSection  $project_setting_section
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProjectSettingGroup $project_setting_group, ProjectSettingSection $project_setting_section)
    {
        if (! $project_setting_section->remove()) {
            return response([
                'message' => __('mabrouk/project_settings/project_setting_sections.cant_destroy'),
            ], 409);
        }

        return response([
            'message' => __('mabrouk/project_settings/project_setting_sections.destroy'),
        ]);
    }
}
