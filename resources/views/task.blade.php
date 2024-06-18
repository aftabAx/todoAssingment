<!DOCTYPE html>
<html>
<head>
    <title>Todo App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: Arial, sans-serif;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        .container h1 {
            margin: 0 0 20px;
            font-size: 24px;
            text-align: center;
        }
        .task-form {
            display: flex;
            margin-bottom: 20px;
        }
        .task-form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
        }
        .task-form button {
            padding: 10px;
            border: none;
            background: #6a11cb;
            color: #fff;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        .task-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .task-list li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .task-list li button {
            background: #ff4d4d;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .task-list li button:hover {
            background: #ff1a1a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Todo App</h1>
        <form class="task-form" id="task-form">
            <input type="text" id="title" placeholder="Add your new todo" required>
            <button type="submit"><i class="fas fa-plus"></i></button>
        </form>
        <ul class="task-list" id="task-list"></ul>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const taskList = document.getElementById('task-list');
            const taskForm = document.getElementById('task-form');

            // Fetch tasks
            fetch('/api/tasks')
                .then(response => response.json())
                .then(data => {
                    data.tasks.forEach(task => {
                        addTaskToDOM(task);
                    });
                });

            // Add task
            taskForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const title = document.getElementById('title').value;

                fetch('/api/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ title })
                })
                .then(response => response.json())
                .then(data => {
                    addTaskToDOM(data.task);
                    taskForm.reset();
                });
            });

            // Delete task
            taskList.addEventListener('click', function (e) {
                if (e.target.tagName === 'BUTTON' || e.target.classList.contains('fa-trash')) {
                    const taskId = e.target.closest('li').dataset.id;

                    fetch(`/api/tasks/${taskId}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(() => {
                        e.target.closest('li').remove();
                    });
                }
            });

            function addTaskToDOM(task) {
                const li = document.createElement('li');
                li.textContent = task.title;
                li.dataset.id = task.id;

                const deleteButton = document.createElement('button');
                deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                li.appendChild(deleteButton);

                taskList.appendChild(li);
            }
        });
    </script>
</body>
</html>
