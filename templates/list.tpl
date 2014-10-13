<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>List Solutions</title>
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="static/lib/bootstrap/dist/css/bootstrap.css">
    <link rel="stylesheet" href="static/lib/bootstrap/dist/css/bootstrap-theme.css">
</head>
<body>


<div class="container body">
    <div class="row">
        <article class="col-lg-12 col-md-12">
            <table class="table table-responsive">
                <thead>
                <tr>
                    <th>Организация и город</th>
                    <th>Программный продукт</th>
                    <th>Отрасль</th>
                    <th>Область автоматизации</th>
                    <th>АРМ</th>
                    <th>Дата внедрения</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($solutions as $solution): ?>
                <tr>
                    <td><?=($solution["organization"] . '</br>' . $solution["city"]);?></td>
                    <td><?=$solution["typeSolution"]?></td>
                    <td><?=$solution["industry"]?></td>
                    <td><?=$solution["functions"]?></td>
                    <td><?=$solution["armCount"]?></td>
                    <td><?=$solution["date"]?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </article>
    </div>
</div>

<footer class="row">
    <div class="col-md-3 pull-right">
        <p>© 2014 <a href="https://github.com/orbisnull">orbisnull</a></p>
    </div>
</footer>


</body>
</html>