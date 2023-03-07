<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    <form action="/assign-users" method="post" id="shipment_assign">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Assign Return</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">        
            <input type="hidden" name="shipment_id" id="shipment_id" value="" />            
            <div class="col-12">
              <label>Select User</label>
              <select name="to_user" class="form-control select2" id="to_user">
                  @foreach($users as $sitekey => $user )
                      <option value="{{$user->id}}">{{$user->name}} - {{$user->email}} </option>                          
                  @endforeach
              </select>
            </div>        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Assign</button>
      </div>
      </form>
    </div>
  </div>
</div>