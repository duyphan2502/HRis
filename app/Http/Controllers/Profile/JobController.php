<?php namespace HRis\Http\Controllers\Profile;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use HRis\Employee;
use HRis\Http\Controllers\Controller;
use HRis\Http\Requests\Profile\JobRequest;
use HRis\JobHistory;
use Illuminate\Support\Facades\Redirect;

/**
 * @Middleware("auth")
 */
class JobController extends Controller {

    /**
     * @var
     */
    protected $user;

    /**
     * @param Sentry $auth
     * @param Employee $employee
     * @param JobHistory $job_history
     */
    public function __construct(Sentry $auth, Employee $employee, JobHistory $job_history)
    {
        parent::__construct($auth);

        $this->employee = $employee;
        $this->job_history = $job_history;
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
    public function index(JobRequest $request, $employee_id = null)
    {
        $employee = $this->employee->getEmployeeById($employee_id, $this->loggedUser->id);

        $this->data['employee'] = $employee;
        $this->data['job_histories'] = $employee->orderedJobHistories();

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
    public function show(JobRequest $request, $employee_id = null)
    {
        $employee = $this->employee->getEmployeeById($employee_id, $this->loggedUser->id);

        $this->data['employee'] = $employee;
        $this->data['job_histories'] = $employee->orderedJobHistories();

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
    public function update(JobRequest $request)
    {
        $id = $request->get('id');

        $employee = $this->employee->whereId($id)->first();
        $job_history = $this->job_history;

        $job_history->create($request->all());

        $employee->update($request->only('joined_date', 'probation_end_date', 'permanency_date'));

        return Redirect::to($request->path())->with('success', 'Record successfully updated.');
    }
}
