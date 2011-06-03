<html>
<head></head>
<body>
    {ff:merge="index" title="blag" foo="bar"}
    <h1>TEST PAGE</h1>
    {ff:feed="test-feed"}
    <div>
        <strong>{entry_title} - {entry_id}</strong><br/>
        {test-field color="green"} - {test-date format="Year: %Y Month: %m Day: %d - %h:%i %a"}
    </div>
    {/ff:feed}
</body>
</html>