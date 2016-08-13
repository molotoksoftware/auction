<div style="padding:5px;font-size:80%;">
<h2>What is an Exception</h2>
<p>With PHP 5 came a new object oriented way of dealing with errors.</p>
<p>Exception handling is used to change the normal flow of the code execution if
a specified error (exceptional) condition occurs. This condition is called an
exception.<br />
<br />
This is what normally happens when an exception is triggered:</p>
<ul>
	<li>The current code state is saved</li>
	<li>The code execution will switch to a predefined (custom) exception
	handler function</li>
	<li>Depending on the situation, the handler may then resume the execution
	from the saved code state, terminate the script execution or continue the
	script from a different location in the code</li>
</ul>
<p><b>Note:</b> Exceptions should only be used with error conditions, and should not be used
to jump to another place in the code at a specified point.</p>
</div>