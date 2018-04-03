@foreach ($oPlatforms as $oNavPlatform)
	<?php $class = $oNavPlatform->id == $iPlatformId ? 'current' : ''; ?>
    @if($oNavPlatform->type==2)
	    <li class="{{ $class }}" id='quie-menu-{{ $oNavPlatform->id }}'><a href="{{ route('user-recharges.quick', $oNavPlatform->id) }}"><span>{{ $oNavPlatform->display_name }}</span></a></li>
    @else
        <li class="{{ $class }}"><a href="{{ route('bank-deposits.netbank',$oNavPlatform->id) }}"><span>{{ $oNavPlatform->display_name }}</span></a></li>
    @endif
@endforeach
