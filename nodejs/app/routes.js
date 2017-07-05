module.exports = function (app, driver) {

    app.get('/wiki/:subString', function (req, res) {

        var subString = req.params.subString
        var final = []
        var session = driver.session();

        session.run(
                'start n = node(*) where n.title =~ {subString} return n.title LIMIT 10', {
                    subString: ".*" + subString + ".*"
                })
            .subscribe({
                onNext: function (record) {
                    title = record.get('n.title');
                    final.push(title);
                },
                onCompleted: function () {
                    session.close();
                    res.json(final);
                },
                onError: function (error) {
                    console.log(error);
                    res.json({"status":"error", "code":"NO_CONNECTION"}); // return all todos in JSON format
                }
            });

    });

    app.get('/wiki/:start/:finish', function (req, res) {

        var start = req.params.start
        var finish = req.params.finish
        var session = driver.session();

        session.run("MATCH (p0:Page {title: {startParam} }), (p1:Page {title: {endParam} }),p = shortestPath((p0)-[*..7]->(p1)) RETURN p AS name", {
                startParam: start,
                endParam: finish
            })
            .subscribe({
                onNext: function (record) {
                    result = record.get('name')
                    res.json(result.segments);
                },
                onCompleted: function () {
                    session.close();
                },
                onError: function (error) {
                    console.log(error);
                    res.json({"status":"error", "code":"NO_CONNECTION"}); // return all todos in JSON format
                }
            });

    })

    // application -------------------------------------------------------------
    app.get('*', function (req, res) {
        res.sendFile(__dirname + '/../../public/index.html'); // load the single view file (angular will handle the page changes on the front-end)
    });
};
