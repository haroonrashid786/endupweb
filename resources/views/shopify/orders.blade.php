<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0,user-scalable=0" />
    <title>Shopify Dashboard | ENDUP TECH</title>
    <style>
        body {
            background-color: #508e5b;
            font-family: Arial, sans-serif;
            color: #ffffff;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background-color: #ffffff;
            color: #508e5b;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .store-logo {
            max-width: 100px;
        }

        .header-title {
            font-size: 24px;
        }

        .contact-btn {
            background-color: #508e5b;
            color: #ffffff;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .dashboard {
            flex: 1;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ffffff;
        }

        table th {
            background-color: #508e5b;
        }

        .order-details-btn {
            background-color: #508e5b;
            color: #ffffff;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .order-details-popup {
            background-color: #ffffff;
            color: #000000;
            max-width: 400px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
        }

        .close-btn {
            float: right;
            cursor: pointer;
        }

        footer {
            background-color: #ffffff;
            color: #508e5b;
            padding: 10px;
            text-align: center;
        }

        .footer-logo {
            max-width: 50px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }

        .footer-links a {
            color: #508e5b;
            margin: 0 10px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <header>
        <img class="store-logo" src="https://enduptech.com/assets/images/icons/logo-green.svg" alt="ENDUP TECH">
        <a href="https://enduptech.com/contact" target="_blank"><button class="contact-btn">Contact Us</button></a>
    </header>
    <!-- HEADER -->
    <div class="dashboard">
    <h2>Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Order Number</th>
                    <th>Customer Name</th>
                    <th>Customer Email</th>
                    <th>Customer Address</th>
                    <th>Customer Phone</th>
                    <th>Order Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{$order->id}}</td>
                    <td>{{$order->order_number}}</td>
                    <td>{{$order->enduser_name}}</td>
                    <td>{{$order->enduser_email}}</td>
                    <td>{{$order->enduser_address}}</td>
                    <td>{{$order->enduser_mobile}}</td>
                    <td>{{$order->delivery_status}}</td>
                    <td><button class="order-details-btn" onclick="showOrderDetails('{{$order->id}}')">View Details</button></td>
                </tr>
            @endforeach

            </tbody>
        </table>
        <div class="order-details-popup" id="orderDetailsPopup">
            <span class="close-btn" onclick="hideOrderDetails()">&times;</span>
            <h3>Order Details</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                  
                </tbody>
            </table>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <img class="footer-logo" src="https://enduptech.com/assets/images/icons/logo-green.svg" alt="Footer Logo">
        <div>&copy; 2023 ENDUP TECH. All rights reserved.</div>
        <div class="footer-links">
            <a href="https://enduptech.com/contact" target="_blank">Contact Us</a>
            <a href="https://enduptech.com/privacy-policy" target="_blank">Privacy Policy</a>
        </div>
    </footer>
    <!-- FOOTER -->

    <script>
       function showOrderDetails(orderId) {
    // AJAX call to fetch order details
    fetch(`/api/orders/${orderId}`)
        .then(response => response.json())
        .then(data => {
            // Populate the popup with order details
            const popupTableBody = document.querySelector('#orderDetailsPopup tbody');
            popupTableBody.innerHTML = '';

            data.items.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price}</td>
                `;
                popupTableBody.appendChild(row);
            });

            // Show the popup
            const popup = document.querySelector('#orderDetailsPopup');
            popup.style.display = 'block';
        })
        .catch(error => {
            console.error('Error fetching order details:', error);
        });
}

        function hideOrderDetails() {
            const popup = document.getElementById('orderDetailsPopup');
            popup.style.display = 'none';
        }
    </script>
</body>

</html>
