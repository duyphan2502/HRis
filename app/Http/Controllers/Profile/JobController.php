<?php namespace HRis\Http\Controllers\Profile;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use HRis\Employee;
use HRis\Http\Controllers\Controller;
use HRis\Http\Requests\Profile\JobRequest;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;

/**
 * @Middleware("auth")
 */
class JobController extends Controller {

    protected $user;

    public function __construct(Sentry $auth, Employee $employee)
    {
        parent::__construct($auth);

        $this->employee = $employee;
    }

    /**
     * Show the profile contact details.
     *
     * @Get("profile/job")
     * @Get("pim/employee-list/{id}/job")
     *
     * @param JobRequest $request
     * @param null $employee_id
     * @return \Illuminate\View\View
     */
    public function job(JobRequest $request, $employee_id = null)
    {
        $employee = $this->employee->getEmployeeById($employee_id, $this->loggedUser->id);

        $this->data['employee'] = $employee;

        $this->data['disabled'] = 'disabled';
        $this->data['pim'] = $request->is('*pim/*') ? true : false;
        $this->data['pageTitle'] = $this->data['pim'] ? 'Employee Job Details' : 'My Job Details';

        return $this->template('pages.profile.job.view');
    }

    /**
     * Show the profile contact details form.
     *
     * @Get("profile/job/edit")
     * @Get("pim/employee-list/{id}/job/edit")
     *
     * @param JobRequest $request
     * @param null $employee_id
     * @return \Illuminate\View\View
     */
    public function showJobEditForm(JobRequest $request, $employee_id = null)
    {
        $employee = $this->employee->getEmployeeById($employee_id, $this->loggedUser->id);

        $this->data['employee'] = $employee;

        $this->data['disabled'] = '';
        $this->data['pim'] = $request->is('*pim/*') ? true : false;
        $this->data['pageTitle'] = $this->data['pim'] ? 'Edit Employee Job Details' : 'Edit My Job Details';

        return $this->template('pages.profile.job.edit');
    }

    /**
     * Updates the profile contact details.
     *
     * @Patch("profile/job")
     * @Patch("pim/employee-list/{id}/job")
     * @param JobRequest $request
     */
    public function updateJob(JobRequest $request)
    {
        $id = $request->get('id');

        $employee = $this->employee->whereId($id)->first();

        $employee->job_title_id = $request->get('job_title_id');
        $employee->employment_status_id = $request->get('employment_status_id');
        $employee->department_id = $request->get('department_id');
//        $employee->effective_date = $request->get('effective_date');
        $employee->joined_date = $request->get('joined_date');
        $employee->probation_end_date = $request->get('probation_end_date');
        $employee->permanency_date = $request->get('permanency_date');

        $employee->save();

        return Redirect::to($request->path());
    }
}