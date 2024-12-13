<html>
<body style="background-color:#EDF2F7; overflow:scroll">
	<div style="text-align:center;margin:0 auto;padding-top:2em;padding-bottom:1em;">
        {{-- <h2 style="margin-top:3px"><img src="{{ $url }}/images/log.png" width="35px" height="35px"></h2> --}}
        <h2 style="margin:0;text-align:center;color:black;font-size:1.8em;">
            {{ isset($organizationDetails['organization_name']) ? $organizationDetails['organization_name'] : 'Organization Name' }}
        </h2>
        <div style="text-align:center;color:black;line-height:1.5;">
            <h3 style="margin:0;">
                Contact Address: {{ isset($organizationDetails['organization_address']) ? $organizationDetails['organization_address'] : 'Address not provided' }}
            </h3>
            <h3 style="margin:0;">
                Phone Number: {{ isset($organizationDetails['organization_phone_number']) ? $organizationDetails['organization_phone_number'] : 'Phone number not provided' }}
            </h3>
            <h3 style="margin:0;">
                Email: {{ isset($organizationDetails['organization_email']) ? $organizationDetails['organization_email'] : 'Email not provided' }}
            </h3>
        </div>
    </div>
	<div style="background-color:#fff;width:70%;margin:0 auto;padding:2em;color:black;">
		<h3>Hello {{ $user['first_name'] ?? ' ' }}</h3>
		
		<p>
			{!! isset($first_paragraph) ? $first_paragraph : '' !!}
		</p>
		<p>
			{!! isset($second_paragraph) ? $second_paragraph : '' !!}
		</p>

		<?php if (isset($btn_label) && isset($frontendUrl)): ?>
			<button style="text-align:center;padding:1em;background-color:blue;color:white;margin:2em auto;display:block;border:none;border-radius:0.2em">
				<a href="{{ $frontendUrl }}" style="text-decoration:none;color:white">{{ $btn_label }}</a>
			</button>
			
			<p>You can copy and paste the below URL into the browser if you are having any challenge clicking on the button:</p>
			
			<p style="word-wrap: break-word;">
				{{ $frontendUrl }}
			</p>
		<?php endif; ?>
		
		<p>
			Best regards<br>
			{{ isset($organizationDetails['organization_name']) ? $organizationDetails['organization_name'] : 'Organization Name' }}
		</p>
	</div>

	<p style="font-size: 12px; text-align: center; margin:2em; padding-bottom: 2em;">
    	&copy; {{ date('Y') }} {{ isset($organizationDetails['organization_name']) ? $organizationDetails['organization_name'] : 'Organization Name' }}
	</p>
</body>
</html>
