{ff:merge="header" title="Welcome to Feed Forge"/}
<body>
    <div id="main">
{ff:feed="demo-feed"}
        <h1>{welcome-text/}</h1>
        <p>{welcome-message/}</p>
        <p><strong>Cinco De Mayo: </strong>{cinco-de-mayo format="n/j/Y"/}</p>
        {relation}
        <strong>{test-related/}</strong><br/>
        {/relation}
{/ff:feed}
    </div>
</body>
</html>