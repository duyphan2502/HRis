<?php

namespace HRis\Http\Controllers\Profile;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Exception;
use HRis\Eloquent\EmergencyContact;
use HRis\Eloquent\Employee;
use HRis\Http\Controllers\Controller;
use HRis\Http\Requests\Profile\EmergencyContactsRequest;
use Illuminate\Support\Facades\Redirect;

/**
 * Class EmergencyContactsController
 * @package HRis\Http\Controllers\Profile
 *
 * @Middleware("auth")
 */
class EmergencyContactsController extends Controller
{

    /**
     * @var Employee
     */
    protected $employee;

    /**
     * @var EmergencyContact
     */
    protected $emergencyContact;

    /**
     * @param Sentry $auth
     * @param Employee $employee
     * @param EmergencyContact $emergencyContact
     */
    public function __construct(Sentry $auth, Employee $employee, EmergencyContact $emergencyContact)
    {
        parent::__construct($auth);

        $this->employee = $employee;
        $this->emergencyContact = $emergencyContact;
    }

    /**
     * Show the Profile - Emergency Contacts.
     *
     * @Get("profile/emergency-contacts")
     * @Get("pim/employee-list/{id}/emergency-contacts")
     *
     * @param EmergencyContactsRequest $request
     * @param null $employee_id
     *
     * @return \Illuminate\View\View
     */
    public function index(EmergencyContactsRequest $request, $employee_id = null)
    {
        $employee = $this->employee->getEmployeeById($employee_id, $this->logged_user->id);

        if ( ! $employee) {
            return Response::make(View::make('errors.404'), 404);
        }

        $this->data['employee'] = $employee;

        $this->data['emergencyContacts'] = $this->emergencyContact->whereEmployeeId($employee->id)->get();

        $this->data['disabled'] = 'disabled';
        $this->data['pim'] = $request->is('*pim/*') ? : false;
        $this->data['pageTitle'] = $this->data['pim'] ? 'Employee Emergency Contacts' : 'My Emergency Contacts';

        return $this->template('pages.profile.emergency-contacts.view');
    }

    /**
     * Save the Profile - Emergency Contacts.
     *
     * @Post("profile/emergency-contacts")
     * @Post("pim/employee-list/{id}/emergency-contacts")
     *
     * @param EmergencyContactsRequest $request
     */
    public function store(EmergencyContactsRequest $request)
    {
        try {
            $this->emergencyContact->create($request->all());

        } catch (Exception $e) {
            return Redirect::to($request->path())->with('danger', UNABLE_UPDATE_MESSAGE);
        }

        return Redirect::to($request->path())->with('success', 'Record successfully added.');
    }

    /**
     * Update the Profile - Emergency Contacts.
     *
     * @Patch("profile/emergency-contacts")
     * @Patch("pim/employee-list/{id}/emergency-contacts")
     *
     * @param EmergencyContactsRequest $request
     */
    public function update(EmergencyContactsRequest $request)
    {
        $emergencyContact = $this->emergencyContact->whereId($request->get('emergency_contact_id'))->first();

        if ( ! $emergencyContact) {
            return Redirect::to($request->path())->with('danger', UNABLE_RETRIEVE_MESSAGE);
        }

        try {
            $emergencyContact->update($request->all());

        } catch (Exception $e) {
            return Redirect::to($request->path())->with('danger', UNABLE_UPDATE_MESSAGE);
        }

        return Redirect::to($request->path())->with('success', 'Record successfully updated.');
    }

    /**
     * Delete the profile emergency contact.
     *
     * @Delete("ajax/profile/emergency-contacts")
     * @Delete("ajax/pim/employee-list/{id}/emergency-contacts")
     *
     * @param EmergencyContactsRequest $request
     */
    public function delete(EmergencyContactsRequest $request)
    {
        if ($request->ajax())
        {
            $emergencyContactId = $request->get('id');

            try
            {
                $this->emergencyContact->whereId($emergencyContactId)->delete();

                print('success');

            } catch (Exception $e)
            {
                print('failed');
            }
        }
    }

    /**
     * Get the profile emergency contact.
     *
     * @Get("ajax/profile/emergency-contacts")
     * @Get("ajax/pim/employee-list/{id}/emergency-contacts")
     *
     * @param EmergencyContactsRequest $request
     */
    public function show(EmergencyContactsRequest $request)
    {
        if ($request->ajax())
        {
            $emergencyContactId = $request->get('id');

            try
            {
                $emergencyContact = $this->emergencyContact->whereId($emergencyContactId)->first();

                print(json_encode($emergencyContact));

            } catch (Exception $e)
            {
                print('failed');
            }
        }
    }
}
