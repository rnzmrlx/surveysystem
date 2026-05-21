<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard</title>

    <link href="/surveysystem/public/user/assets/img/favicon.png" rel="icon">
  <link href="/surveysystem/public/user/assets/img/apple-touch-icon.png" rel="apple-touch-icon">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Bootstrap JS Bundle (includes Popper) — loaded early so dropdowns work -->

  <style>
    :root {
      --ink: #0f0e0d;
      --ink-2: #3a3835;
      --ink-3: #7a776f;
      --paper: #f7f5f0;
      --paper-2: #eceae3;
      --paper-3: #e0ddd4;
      --gold: #c9972b;
      --gold-light: #f5e9cc;
      --gold-dark: #8a6318;
      --teal: #1b6b6b;
      --teal-lt: #d0eaea;
      --rose: #a02c2c;
      --rose-lt: #f5dede;
      --radius: 10px;
      --shadow: 0 2px 16px rgba(15, 14, 13, 0.07);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'DM Sans', sans-serif;
      background: var(--paper);
      color: var(--ink);
      padding-top: 60px;
    }

    .footer {
      margin-left: 260px;
      width: calc(100% - 260px);
      padding: 14px 3rem;
      background: var(--paper);
      border-top: 1.5px solid var(--paper-3);
      font-size: 13px;
      color: var(--ink-3);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      text-align: center;
      gap: 4px;
      transition: all 0.3s;
    }

    body.sidebar-hidden .footer {
      margin-left: 0;
      width: 100%;
    }
  </style>

</head>

<body>