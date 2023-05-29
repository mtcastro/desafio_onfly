@component('mail::message')
    
<h1>New expense registered successfully</h1>
<ul>
    <li><b>description:</b> {{$expense->description}};</li>
    <li><b>amount:</b> R$ {{$expense->amount}};</li>
    <li><b>date:</b> {{\Carbon\Carbon::parse($expense->date)->format('d/m/Y')}}.</li>
</ul>
@endcomponent