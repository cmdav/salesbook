<html>
<body style="background-color:#EDF2F7; overflow:scroll">
	<div style="text-align:center;display:flex;width:250px;margin:0 auto;padding-top:2em;padding-bottom:1em;">
		<h2 style="margin-top:3px"><img src="{{ $url }}/images/log.png" width="35px" height="35px"></h2>
		<h2 style="text-align:center;color:black;font-size:2.2em;">{{ config('app.name') }}</h2>
	</div>
	<div style="background-color:#fff;width:70%;margin:0 auto;padding:2em;color:black;">
		<h3> Hi {{ $user['first_name'] }}</h3>
		
		<p>
			{{ $first_paragraph}}
		</p>
		<p>
			{{ $second_paragraph}}
		</p>
		<button style="text-align:center;padding:1em;background-color:blue;color:white;margin:2em auto;display:block;border:none;border-radius:0.2em">
			
			<a href="{{ $frontendUrl }}" style="text-decoration:none;color:white">{{ $btn_label }}</a>
			
		</button>
		
		<p>You can copy and paste the below url into the browser if you are having any challenge clicking on the button</p>
		
		<p style="word-wrap: break-word;">{{ $frontendUrl }}</p> 
		
		
		<p>
			Best regards<br>
			{{ config('app.name') }}
		</p>
		

	</div>
	<p style="font-size: 12px; text-align: center; margin:2em; padding-bottom: 2em;">
    		&copy; {{ date('Y') }} {{ config('app.name') }} 
    		
	</p>
</body>
</html>
