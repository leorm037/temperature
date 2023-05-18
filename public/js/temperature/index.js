function graphicConstruct(tempArray) {
    var labels = [];
    var cpu = [];
    var gpu = [];
    var sensation = [];
    var temperature = [];

    Array.from(tempArray.result).reverse().map(t => {
        let dateTimeJson = t.dateTime.replace("+00:00","");
        let date = new Date(dateTimeJson);

        labels.push(date.toLocaleDateString() + " " + date.toLocaleTimeString());
        cpu.push(parseFloat(t.cpu));
        gpu.push(parseFloat(t.gpu));
        sensation.push(parseFloat(t.sensation));
        temperature.push(parseFloat(t.temperature));
    });

    var data = {
        labels: labels,
        datasets: [
            {
                label: 'CPU',
                backgroundColor: 'rgba(0,100,255,0.5)',
                borderColor: 'rgb(0,100,255)',
                data: cpu
            },
            {
                label: 'GPU',
                backgroundColor: 'rgba(255,99,132,0.5)',
                borderColor: 'rgb(255,99,132)',
                data: gpu
            },
            {
                label: 'Sensation',
                backgroundColor: 'rgba(0,100,255,0.5)',
                borderColor: 'rgb(0,100,255)',
                data: sensation
            },
            {
                label: 'Temperature',
                backgroundColor: 'rgba(255,99,132,0.5)',
                borderColor: 'rgb(255,99,132)',
                fill: true,
                data: temperature
            }
        ]
    };

    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: function (event, element) {
                const TAG_SPINNER = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Carregando...</span></div>';

                modalDateTime = $('#modalDateTime').html(TAG_SPINNER);                 //01
                modalCpu = $('#modalCpu').html(TAG_SPINNER);                           //02
                modalGpu = $('#modalGpu').html(TAG_SPINNER);                           //03
                modalSensation = $('#modalSensation').html(TAG_SPINNER);               //04
                modalTemperature = $('#modalTemperature').html(TAG_SPINNER);           //05
                modalWindDirection = $('#modalWindDirection').html(TAG_SPINNER);       //06
                modalWindVelocity = $('#modalWindVelocity').html(TAG_SPINNER);         //07
                modalHumidity = $('#modalHumidity').html(TAG_SPINNER);                 //08
                modalWeatherCondition = $('#modalWeatherCondition').html(TAG_SPINNER); //09
                modalPressure = $('#modalPressure').html(TAG_SPINNER);                 //10
                $('#modalDetalhe').modal('show');

                let dateTimeLabel = graphic.data.labels[element[0].index];

                $.post(URL_JSON_DETAIL, {dateTime: dateTimeLabel})
                        .done(function (data) {
                            if (data.message === 'success') {
                                let dJson = data.result.dateTime.replace("+00:00","");
                                let d = new Date(dJson);

                                modalDateTime.text(d.toLocaleDateString() + " " + d.toLocaleTimeString());                     //01
                                modalCpu.text(parseFloat(data.result.cpu).toLocaleString(LOCALE) + " ºC");                     //02
                                modalGpu.text(parseFloat(data.result.gpu).toLocaleString(LOCALE) + " ºC");                     //03
                                modalSensation.text(parseFloat(data.result.sensation).toLocaleString(LOCALE) + " ºC");         //04
                                modalTemperature.text(parseFloat(data.result.temperature).toLocaleString(LOCALE) + " ºC");     //05
                                modalWindDirection.text(data.result.windDirection);                                            //06
                                modalWindVelocity.text(parseFloat(data.result.windVelocity).toLocaleString(LOCALE) + " km/h"); //07
                                modalHumidity.text(parseFloat(data.result.humidity).toLocaleString(LOCALE) + "%");             //08
                                modalWeatherCondition.html(data.result.weatherCondition + " <img src='/realistic/70px/" + data.result.icon + ".png'>");                                      //09
                                modalPressure.text(parseFloat(data.result.pressure).toLocaleString(LOCALE) + " hPa");          //10
                            } else {
                                console.log("Fail",dateTimeLabel);
                            }
                        }).fail(function (data) {
                            console.log("Fail",dateTimeLabel,data);
                        });
            },
            responsive: true,
            elements: {
                line: {
                    borderWidth: 1
                },
                point: {
                    pointStyle: 'circle',
                    pointRadius: 1,
                    pointHoverRadius: 5
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    position: 'average',
                    callbacks: {
                        label: function (tooltipItems) {
                            return " " +
                                    tooltipItems.dataset.label +
                                    ": " +
                                    tooltipItems.raw + " ºC";
                        }
                    }
                }
            }
        }
    };
    const graphic = new Chart(document.getElementById('temperature'), config);
}

function graphicShow(days) {
    $.post(URL_JSON_DAYS, {days: days})
            .done(function (data, textStatus, jqXHR) {
                graphicConstruct(data);
            })
            .fail(function (data) {
                console.log("Message", data.responseText);
            });
}