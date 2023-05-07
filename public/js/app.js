function graphicConstruct(temperatures, temperatureTrans, sensationTrans) {
    var labels = [];
    var cpu = [];
    var gpu = [];
    var temperature = [];
    var sensation = [];
    var windDirection = [];
    Array.from(temperatures).reverse().map(temp => {
        let data = new Date(temp.dateTime.date);
        labels.push(data.toLocaleDateString() + " " + data.toLocaleTimeString());
        cpu.push(temp.cpu);
        gpu.push(temp.gpu);
        temperature.push(temp.temperature);
        sensation.push(temp.sensation);
        windDirection.push(temp.windDirection);
    });
    var data = {
        labels: labels,
        datasets: [
            {
                label: 'CPU',
                backgroundColor: 'rgba(65, 65, 65,0.5)',
                borderColor: 'rgb(65, 65, 65)',
                data: cpu
            },
            {
                label: 'GPU',
                backgroundColor: 'rgba(180, 180, 180,0.5)',
                borderColor: 'rgb(180, 180, 180)',
                data: gpu
            },
            {
                label: temperatureTrans,
                backgroundColor: 'rgba(0,100,255,0.5)',
                borderColor: 'rgb(0,100,255)',
                data: temperature
            },
            {
                label: sensationTrans,
                backgroundColor: 'rgba(255,99,132,0.5)',
                borderColor: 'rgb(255,99,132)',
                fill: true,
                data: sensation
            }
        ]
    };
    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            onClick: function (event, element) {
                modalDateTime = $("#modalDateTime").text("").attr('class', 'spinner-border spinner-border-sm');
                modalCPU = $("#modalCPU").text("").attr('class', 'spinner-border spinner-border-sm');
                modalGPU = $("#modalGPU").text("").attr('class', 'spinner-border spinner-border-sm');
                modalTemperature = $("#modalTemperature").text("").attr('class', 'spinner-border spinner-border-sm');
                modalSensation = $("#modalSensation").text("").attr('class', 'spinner-border spinner-border-sm');
                modalWindDirection = $("#modalWindDirection").text("").attr('class', 'spinner-border spinner-border-sm');
                modalWindVelocity = $("#modalWindVelocity").text("").attr('class', 'spinner-border spinner-border-sm');
                modalHumidity = $("#modalHumidity").text("").attr('class', 'spinner-border spinner-border-sm');
                modalWeatherCondition = $("#modalWeatherCondition").text("").attr('class', 'spinner-border spinner-border-sm');
                
                

                $.post(URL_JSON_DETAIL, {dateTime: graphic.data.labels[element[0].index]})
                        .done(function (data) {
                            let d = new Date(data.dateTime.date);

                            modalDateTime.removeAttr('class').text(
                                    d.getDay().toString().padStart(2, '0') + "/" +
                                    d.getMonth().toString().padStart(2, '0') + "/" +
                                    d.getFullYear() + " " +
                                    d.getHours().toString().padStart(2, '0') + ":" +
                                    d.getMinutes().toString().padStart(2, '0') + ":" +
                                    d.getSeconds());
                            modalCPU.removeAttr('class').text(parseFloat(data.cpu).toLocaleString(LOCALE.replace("_", "-")) + " ºC");
                            modalGPU.removeAttr('class').text(parseFloat(data.gpu).toLocaleString(LOCALE.replace("_", "-")) + " ºC");
                            modalTemperature.removeAttr('class').text(parseFloat(data.temperature).toLocaleString(LOCALE.replace("_", "-")) + " ºC");
                            modalSensation.removeAttr('class').text(parseFloat(data.sensation).toLocaleString(LOCALE.replace("_", "-")) + " ºC");
                            modalWindDirection.removeAttr('class').text(data.windDirection);
                            modalWindVelocity.removeAttr('class').text(parseFloat(data.windVelocity).toLocaleString(LOCALE.replace("_", "-")) + " km/h");
                            modalHumidity.removeAttr('class').text(parseFloat(data.humidity).toLocaleString(LOCALE.replace("_", "-")) + "%");
                            modalWeatherCondition.removeAttr('class').text(data.weatherCondition);
                        });
                $('#modalDetalhe').modal('show');
            },
            scales: {
                y: {
                    grid: {
                        lineWidth: function (context) {
                            if (context.tick.value === 35) {
                                return 2;
                            }
                            if (context.tick.value === 60) {
                                return 5;
                            }
                            return 1;
                        },
                        color: function (context) {
                            if (context.tick.value === 35) {
                                return "rgba(0,0,0,0.5)";
                            }
                            if (context.tick.value === 60) {
                                return "rgba(256,0,0,0.5)";
                            }
                            return Chart.defaults.borderColor;
                        }
                    }
                }
            },
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
                                    tooltipItems.formattedValue +
                                    ' ºC';
                        }
                    }
                }
            }
        }
    };
    const graphic = new Chart(document.getElementById('temperature'), config);
}

function graphicShow(url, temperatureTrans, sensationTrans, messageTrans) {
    $("body").loading({
        message: messageTrans,
        onStart: function (loading) {
            loading.overlay.slideDown();
        },
        onStop: function (loading) {
            loading.overlay.delay(400).slideUp();
        }
    });
    $.ajax({
        url: url
    }).done(function (temperatures) {
        graphicConstruct(temperatures, temperatureTrans, sensationTrans);
    });
    $("body").loading('stop');
}

function updateLastTemperature(url) {
    $.ajax({
        url: url,
        beforeSend: function () {
            $("#temp-thermometer").text("");
            $("#icon-thermometer").removeAttr('class').attr('class', 'spinner-grow spinner-grow-sm');
        }
    }).done(function (lastTemperature) {
        let temperature = parseFloat(lastTemperature.temperature);
        //let sensation = parseFloat(lastTemperature.sensation);

        $("#temp-thermometer").text(temperature + "ºC");

        if (temperature <= 10.00) {
            $("#icon-thermometer").removeAttr('class').attr('class', 'bi bi-thermometer-snow');
        } else if (temperature <= 20) {
            $("#icon-thermometer").removeAttr('class').attr('class', 'bi bi-thermometer-low');
        } else if (temperature > 20) {
            $("#icon-thermometer").removeAttr('class').attr('class', 'bi bi-thermometer-low');
        } else if (temperature > 25) {
            $("#icon-thermometer").removeAttr('class').attr('class', 'bi bi-thermometer-sun');
        } else {
            $("#icon-thermometer").removeAttr('class').attr('class', 'bi bi-thermometer-half');
        }
    }).fail(function () {
        $("#temp-thermometer").text("");
        $("#icon-thermometer").removeAttr('class').attr('class', 'bi bi-thermometer-half');
    });
}