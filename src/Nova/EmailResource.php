<?php

namespace AppsInteligentes\EmailTracking\Nova;

use AppsInteligentes\EmailTracking\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;


/**
 * @method Email model()
 */
class EmailResource extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = Email::class;
    public static $group = 'Logs';
    public static $globallySearchable = false;

    /**
     * Indicates whether the resource should automatically poll for new resources.
     *
     * @var bool
     */
    public static $polling = true;


    /**
     * Indicates whether to show the polling toggle button inside Nova.
     *
     * @var bool
     */
    public static $showPollingToggle = true;

    public static function getModel()
    {
        return Email::class;
    }

    /**
     * Determine if this resource is available for navigation.
     *
     * @param Request $request
     * @return bool
     */
    public static function availableForNavigation(Request $request): bool
    {
        return Gate::allows('viewAny', Email::class);
    }

    public static function label()
    {
        return __('email-tracking::resources.emails');
    }

    public static function singularLabel()
    {
        return __('email-tracking::resources.email');
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'message_id', 'subject', 'to', 'cc', 'bcc', 'reply_to',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(__('email-tracking::resources.model_id'), 'id'),
            Text::make(__('email-tracking::resources.message_id'), 'message_id'),

            DateTime::make(__('email-tracking::resources.created_at'), 'created_at'),

            DateTime::make(__('email-tracking::resources.updated_at'), 'updated_at'),

            MorphTo::make('sender')->searchable(),

            Panel::make(__('email-tracking::resources.email'), [
                Text::make(__('email-tracking::resources.subject'), 'subject'),
                Text::make(__('email-tracking::resources.mail_to'), 'to'),
                Text::make(__('email-tracking::resources.mail_cc'), 'cc'),
                Text::make(__('email-tracking::resources.mail_bcc'), 'bcc'),
                Text::make(__('email-tracking::resources.mail_reply_to'), 'reply_to'),
            ]),

            Panel::make(__('email-tracking::resources.statistics'), [
                DateTime::make(__('email-tracking::resources.delivered_at'), 'delivered_at'),

                DateTime::make(__('email-tracking::resources.failed_at'), 'failed_at'),

                Text::make(__('email-tracking::resources.status'), function (Email $email) {
                    $array = explode('||', $email->delivery_status_message);

                    return implode('<br /><br />', $array);
                })->asHtml(),

                Number::make(__('email-tracking::resources.delivery_status_attempts'), 'delivery_status_attempts'),
                Number::make(__('email-tracking::resources.opened'), 'opened'),

                DateTime::make(__('email-tracking::resources.first_opened_at'), 'first_opened_at'),
                DateTime::make(__('email-tracking::resources.last_opened_at'), 'last_opened_at'),

                Number::make(__('email-tracking::resources.clicked'), 'clicked'),

                DateTime::make(__('email-tracking::resources.first_clicked_at'), 'first_clicked_at'),
                DateTime::make(__('email-tracking::resources.last_clicked_at'), 'last_clicked_at'),

            ]),
        ];
    }

    public function fieldsForIndex(NovaRequest $request)
    {
        return [

            ID::make()->sortable(),

            DateTime::make(__('email-tracking::resources.created_at'), 'created_at')->sortable(),

            MorphTo::make('sender')->searchable(),

            Text::make(__('email-tracking::resources.subject'), 'subject')->sortable(),
            Text::make(__('email-tracking::resources.mail_to'), 'to')->sortable(),

            Number::make(__('email-tracking::resources.delivered'), fn() => null)
                ->textAlign('center')
                ->canSee(fn() => empty($this->model()->delivered_at) && empty($this->model()->failed_at)),

            //delivered_at or failed_at are defined
            Boolean::make(__('email-tracking::resources.delivered'), fn() => isset($this->model()->delivered_at))
                ->canSee(fn() => !empty($this->model()->delivered_at) || !empty($this->model()->failed_at)),

        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
