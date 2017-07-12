module.exports = function (app, driver) {

    app.get('/autocomplete/:subString', function (req, res) {

        var subString = req.params.subString
        var session = driver.session();

        session.run(
                'start n = node(*) where n.title =~ {subString} return n.title LIMIT 10', {
                    subString: ".*" + subString + ".*"
                })
            .then(function (result) {
                var titles = []
                result.records.forEach(function (record) {
                    title = record.get('n.title');
                    titles.push(title);
                });

                session.close();
                    res.json({
                        "status": "SUCCESS",
                        "titles": titles
                    });
            })
            .catch(function (error) {
                console.log(error);
                res.json({
                    "status": "ERROR",
                    "code": "NO_CONNECTION"
                });
            });

    });

    app.get('/path/:start/:finish', function (req, res) {

        var start = req.params.start
        var finish = req.params.finish
        var session = driver.session();

        session.run("MATCH (p0:Page {title: {startParam} }), (p1:Page {title: {endParam} }),p = shortestPath((p0)-[*..7]->(p1)) RETURN p AS name", {
                startParam: start,
                endParam: finish
            })
            .then(function (result) {

                if (result.records.length == 0) {
                    res.json({
                        "status": "NO_PATH_FOUND"
                    });
                } else {

                    result.records.forEach(function (record) {
                        response = record.get('name').segments
                    });

                    var steps = response.map(function (item) {
                        return item.start.properties.title;
                    });

                    if (steps.length > 0 ){
                        steps.push(finish)
                    }

                    console.log(steps)

                    res.json({
                        "status": "SUCCESS",
                        "steps": steps
                    });
                }

                session.close();
            })
            .catch(function (error) {
                console.log(error);
                res.json({
                    "status": "ERROR",
                    "code": "NO_CONNECTION"
                });

            });

    })

    app.get('/random', function (req, res) {

        var start = req.params.start
        var finish = req.params.finish
        var session = driver.session();

        session.run("MATCH (n) WHERE rand() <= 0.0001  RETURN n.title AS random LIMIT 1")
            .subscribe({
                onNext: function (record) {
                    result = record.get('random')
                    res.json({
                        "status": "SUCCESS",
                        "randomTitle": result
                    });                },
                onCompleted: function () {
                    session.close();
                },
                onError: function (error) {
                    console.log(error);
                    res.json({
                        "status": "ERROR",
                        "code": "NO_CONNECTION"
                    });
                }
            });

    })

    // application -------------------------------------------------------------
    app.get('*', function (req, res) {
        res.sendFile(__dirname + '/../../public/index.html'); // load the single view file (angular will handle the page changes on the front-end)
    });
};
