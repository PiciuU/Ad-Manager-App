<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Http\Requests\AdRequest;
use App\Http\Resources\AdResource;
use App\Http\Resources\AdCollection;

class AdController extends Controller
{
    private $logController;

    public function __construct(LogController $logController = new LogController())
    {
        $this->logController = $logController;
    }
    /**
     * Checks if the user has administrator privileges.
     *
     * @return bool
     */
    public function hasAccess()
    {
        return auth()->user()->hasAdminPrivileges();
    }

    /**
     * =====================
     *     ADMIN SECTION
     * =====================
     */

    /**
     * Retrieve a simplified list of all ads.
     * This method is accessible only to administrators.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function indexAsAdmin()
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ads = new AdCollection(Ad::orderBy('created_at', 'desc')->get());

        $ads->returnFields(['id', 'name']);

        return $this->successResponse("Ads has been successfully found.", $ads);
    }

    /**
     * Retrieve a simplified list of user ads.
     * This method is accessible only to administrators.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function indexShowAsAdmin($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ads = new AdCollection(Ad::where('user_id', $id)->orderBy('created_at', 'desc')->get());

        $ads->returnFields(['id', 'name']);

        return $this->successResponse("Ads has been successfully found.", $ads);
    }

    /**
     * Retrieve a specific ad by its ID.
     * This method is accessible only to administrators.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function showAsAdmin($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::find($id);

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        return $this->successResponse("Ad has been successfully found.", new AdResource($ad));
    }

    /**
     * Store a new ad.
     * This method is accessible only to administrators.
     *
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function storeAsAdmin(AdRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::create($request->validated());

        if (!$ad) return $this->errorResponse("An error occurred while creating the ad, try again later.", 500);

        $invoiceController = new InvoiceController();

        $invoice = $invoiceController->createInvoice([
            'ad_id' => $ad->id,
            'ad_start_date' => $ad->ad_start_date,
            'ad_end_date' => $ad->ad_end_date,
        ]);

        $this->logController->createLogEntry('ADMIN/AD/CREATE', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Ad has been successfully created.", new AdResource($ad));
    }

    /**
     * Update an existing ad.
     * This method is accessible only to administrators.
     *
     * @param  int  $id
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function updateAsAdmin($id, AdRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        if (!$ad->update($request->validated())) return $this->errorResponse("An error occurred while updating the ad, try again later.", 500);

        $this->logController->createLogEntry('ADMIN/AD/UPDATE', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Ad has been successfully updated.", new AdResource($ad));
    }

    /**
     * Deactivate an ad by setting its status to 'inactive'.
     * This method is accessible only to administrators.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function deactivateAsAdmin($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        if (!$ad->update(['status' => 'inactive'])) return $this->errorResponse("An error occurred while deactivating the ad, try again later.", 500);

        $this->logController->createLogEntry('ADMIN/AD/DEACTIVATE', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Ad has been successfully deactivated.", new AdResource($ad));
    }

    /**
     * Renew an ad by updating its status to 'unpaid' and creating a new invoice.
     * This method is accessible only to administrators.
     *
     * @param  int  $id
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function renewAsAdmin($id, AdRequest $request)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $validatedData = $request->validated();
        $validatedData['status'] = 'unpaid';

        if (!$ad->update($validatedData)) return $this->errorResponse("An error occurred while renewing the ad, try again later.", 500);

        $invoiceController = new InvoiceController();

        $invoice = $invoiceController->createInvoice([
            'ad_id' => $ad->id,
            'ad_start_date' => $ad->ad_start_date,
            'ad_end_date' => $ad->ad_end_date,
        ]);

        $this->logController->createLogEntry('ADMIN/AD/RENEW', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Ad has been successfully renewed.", [ 'advert' => new AdResource($ad), 'invoice' => $invoice]);
    }

     /**
     * Delete an ad.
     * This method is accessible only to administrators.
     * (Currently not used)
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function deleteAsAdmin($id)
    {
        if (!$this->hasAccess()) return $this->errorResponse("You do not have access to this resource!", 403);

        $ad = Ad::where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        if (!$ad->delete()) return $this->errorResponse("An error occurred while deleting the ad, try again later.", 500);

        return $this->successResponse("Ad has been successfully deleted.");
    }

    /**
     * =====================
     *     USER SECTION
     * =====================
     */

    /**
     * Retrieve a simplified list of ads.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        $ads = new AdCollection(auth()->user()->ads()->select('id', 'name')->orderBy('created_at', 'desc')->get());

        $ads->returnFields(['id', 'name']);

        return $this->successResponse("Ads has been successfully found.", $ads);
    }

    /**
     * Store a new ad.
     *
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(AdRequest $request)
    {
        $ad = Ad::create($request->validated());

        if (!$ad) return $this->errorResponse("An error occurred while creating the ad, try again later.", 500);

        $invoiceController = new InvoiceController();

        $invoice = $invoiceController->createInvoice([
            'ad_id' => $ad->id,
            'ad_start_date' => $ad->ad_start_date,
            'ad_end_date' => $ad->ad_end_date,
        ]);

        $this->logController->createLogEntry('AD/CREATE', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Ad has been successfully created.", new AdResource($ad));
    }

    /**
     * Retrieve a specific ad by its ID.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        return $this->successResponse("Ad has been successfully found.", new AdResource($ad));
    }

    /**
     * Update an existing ad.
     *
     * @param  int  $id
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, AdRequest $request)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        if (!$ad->update($request->validated())) return $this->errorResponse("An error occurred while updating the ad, try again later.", 500);

        $this->logController->createLogEntry('AD/UPDATE', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Ad has been successfully updated.", new AdResource($ad));
    }

    /**
     * Deactivate an ad by setting its status to 'inactive'.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function deactivate($id)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        if (!$ad->update(['status' => 'inactive'])) return $this->errorResponse("An error occurred while deactivating the ad, try again later.", 500);

        $this->logController->createLogEntry('AD/DEACTIVATE', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Ad has been successfully deactivated.", new AdResource($ad));
    }

    /**
     * Renew an ad by updating its status to 'unpaid' and creating a new invoice.
     *
     * @param  int  $id
     * @param  \App\Http\Requests\AdRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function renew($id, AdRequest $request)
    {
        $ad = auth()->user()->ads()->where('id', $id)->first();

        if (!$ad) return $this->errorResponse("Ad not found.", 404);

        $validatedData = $request->validated();
        $validatedData['status'] = 'unpaid';

        if (!$ad->update($validatedData)) return $this->errorResponse("An error occurred while renewing the ad, try again later.", 500);

        $invoiceController = new InvoiceController();

        $invoice = $invoiceController->createInvoice([
            'ad_id' => $ad->id,
            'ad_start_date' => $ad->ad_start_date,
            'ad_end_date' => $ad->ad_end_date,
        ]);

        $this->logController->createLogEntry('AD/RENEW', ['user_id' => auth()->user()->id, 'ad_id' => $ad->id]);

        return $this->successResponse("Ad has been successfully renewed.", [ 'advert' => new AdResource($ad), 'invoice' => $invoice]);
    }
}
