<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests; ///this line of code not Used 
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request) ///Request  Path need Properly add
    {
        if($user_id = $request->get('user_id')) {

            $response = $this->repository->getUsersJobs($user_id);

        }
        elseif($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID')) ///
        ///env File not included 
        {
            $response = $this->repository->getAll($request);
        }

        return response($response); ///response Json Not used properly
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $job = $this->repository->with('translatorJobRel.user')->find($id);

        return response($job);///response Json Not used properly
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();

        $response = $this->repository->store($request->__authenticatedUser, $data);

        return response($response);///response Json Not used properly

    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)///Request  Path need Properly add
    {
        $data = $request->all();
        $cuser = $request->__authenticatedUser;
        $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser); ///array except is unKnown Function

        return response($response);///response Json Not used properly
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)//Request  Path need Properly add
    {
        $adminSenderEmail = config('app.adminemail'); ///unknown config and consider injecting the configuration value via the constructor.
        $data = $request->all();

        $response = $this->repository->storeJobEmail($data);

        return response($response); //response Json Not used properly
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)///Request  Path need Properly add
    {
        if($user_id = $request->get('user_id')) {

            $response = $this->repository->getUsersJobsHistory($user_id, $request);
            return response($response); //response Json Not used properly
        }

        return null;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJob($data, $user);

        return response($response);//response Json Not used properly
    }

    public function acceptJobWithId(Request $request)///Request  Path need Properly add
    {
        $data = $request->get('job_id');
        $user = $request->__authenticatedUser;

        $response = $this->repository->acceptJobWithId($data, $user);

        return response($response);//response Json Not used properly
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->cancelJobAjax($data, $user);

        return response($response);//response Json Not used properly
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();

        $response = $this->repository->endJob($data);

        return response($response);//response Json Not used properly

    }

    public function customerNotCall(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();

        $response = $this->repository->customerNotCall($data);

        return response($response);//response Json Not used properly

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();
        $user = $request->__authenticatedUser;

        $response = $this->repository->getPotentialJobs($user);

        return response($response);//response Json Not used properly
    }

    public function distanceFeed(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();

        if (isset($data['distance']) && $data['distance'] != "") {
            $distance = $data['distance'];
        } else {
            $distance = "";
        }
        if (isset($data['time']) && $data['time'] != "") {
            $time = $data['time'];
        } else {
            $time = "";
        }
        if (isset($data['jobid']) && $data['jobid'] != "") {
            $jobid = $data['jobid'];
        }

        if (isset($data['session_time']) && $data['session_time'] != "") {
            $session = $data['session_time'];
        } else {
            $session = "";
        }

        if ($data['flagged'] == 'true') {
            if($data['admincomment'] == '') return "Please, add comment";
            $flagged = 'yes';
        } else {
            $flagged = 'no';
        }
        
        if ($data['manually_handled'] == 'true') {
            $manually_handled = 'yes';
        } else {
            $manually_handled = 'no';
        }

        if ($data['by_admin'] == 'true') {
            $by_admin = 'yes';
        } else {
            $by_admin = 'no';
        }

        if (isset($data['admincomment']) && $data['admincomment'] != "") {
            $admincomment = $data['admincomment'];
        } else {
            $admincomment = "";
        }
        if ($time || $distance) {

            $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
            ///Distance Models class UnKnown  
            ///Models Not Found
        }

        if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {

            $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));
            ///Job Models Not Found
        }

        return response('Record updated!');//response Json Not used properly
    }

    public function reopen(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();
        $response = $this->repository->reopen($data);

        return response($response);//response Json Not used properly
    }

    public function resendNotifications(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        $job_data = $this->repository->jobToData($job);
        $this->repository->sendNotificationTranslator($job, $job_data, '*');

        return response(['success' => 'Push sent']);//response Json Not used properly
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)///Request  Path need Properly add
    {
        $data = $request->all();
        $job = $this->repository->find($data['jobid']);
        $job_data = $this->repository->jobToData($job);

        try {
            $this->repository->sendSMSNotificationToTranslator($job);
            return response(['success' => 'SMS sent']);//response Json Not used properly
        } catch (\Exception $e) {
            return response(['success' => $e->getMessage()]);//response Json Not used properly
        }
    }

}
