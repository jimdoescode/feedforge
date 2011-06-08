<html>
<head></head>
<body>
    <h1>HOME PAGE</h1>
    <h2>{merge:foo}</h2>
    {ff:feed="demo-feed" order="something"}
        <strong>{field1} - {id}</strong><br/>
        <span style="color: red;">{field2}</span><br/>
    {/ff:feed}
    <br/>
</body>
</html>