@component('mail::message')

# A new Sales Lead has been assigned to you

Lead Number: <strong>{{ $leadNumber }}</strong> <br>
Name: <strong>{{ $leadName }}</strong> <br>
Contact Number: <strong>{{ $leadContactNumber }}</strong> <br>
Email: <strong>{{ $leadEmail }}</strong> <br>


Thanks,<br>
Spanline
@endcomponent
