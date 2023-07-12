import { Select, Chart, initTE } from "tw-elements";



// Get the HTML element
const analyticViewsChart = document.getElementById('analytics-views-chart');

if (analyticViewsChart) {
    // Retrieve the values from data-* attributes
    const labels = JSON.parse(analyticViewsChart.dataset.labels);
    const label1 = analyticViewsChart.dataset.label1;
    const tooltip1 = analyticViewsChart.dataset.tooltip1;
    const data1 = JSON.parse(analyticViewsChart.dataset.values1);
    const label2 = analyticViewsChart.dataset.label2;
    const tooltip2 = analyticViewsChart.dataset.tooltip2;
    const data2 = JSON.parse(analyticViewsChart.dataset.values2);

    const dataBar = {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: label1,
                    tooltip: tooltip1,
                    data: data1,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                },
                {
                    label: label2,
                    tooltip: tooltip2,
                    data: data2,
                    backgroundColor: 'rgba(255, 206, 86, 0.2)',
                    borderColor: 'rgba(255, 206, 86, 1)',
                    borderWidth: 1,
                },
            ],
        },
    };

    const dataBarOptions = {
        options: {
        plugins: {
            tooltip: {
            callbacks: {
                label: function (context) {
                let label = context.dataset.label || "";
                let tooltip = context.dataset.tooltip || "";
                label = `${tooltip}: ${context.formattedValue}`;
                return label;
                },
            },
            },
        },
        },
    };

    new Chart(
        analyticViewsChart,
        dataBar,
        dataBarOptions
    );
}

initTE({ Select }, false); // set second parameter to true if you want to use a debugger
initTE({ Chart });