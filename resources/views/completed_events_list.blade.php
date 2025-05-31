<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Events List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .event-card {
            margin-bottom: 30px;
            display: flex;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .event-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
        .event-content {
            padding: 15px;
            flex: 1;
        }
        .event-title {
            color: #c21313;
            font-weight: bold;
            font-size: 1.25rem;
            margin-bottom: 5px;
        }
        .event-date {
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .event-date svg {
            margin-right: 5px;
        }
        .event-description {
            color: #495057;
            font-size: 0.9rem;
        }
        .container {
            max-width: 800px;
        }
        h1 {
            color: #c21313;
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Completed Events</h1>

        <div class="event-card">
            <img src="{{ asset('img/events/jamming.jpg') }}" alt="Jamming on an Old Saya" class="event-image">
            <div class="event-content">
                <h3 class="event-title">Jamming on an Old Saya</h3>
                <div class="event-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                    May 2 | 5:30 PM – 7:30 PM
                </div>
                <p class="event-description">A cultural music event featuring traditional Filipino instruments and music styles. Join us for an evening of cultural appreciation and musical exploration.</p>
            </div>
        </div>

        <div class="event-card">
            <img src="{{ asset('img/events/jamming.jpg') }}" alt="Jamming on an Old Saya - Session 2" class="event-image">
            <div class="event-content">
                <h3 class="event-title">Jamming on an Old Saya</h3>
                <div class="event-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                    May 5 | 2:00 PM – 4:00 PM
                </div>
                <p class="event-description">Second session of our cultural music event featuring traditional Filipino instruments and music styles. This follow-up session will dive deeper into the techniques and history.</p>
            </div>
        </div>

        <div class="event-card">
            <img src="{{ asset('img/events/molecular.jpg') }}" alt="Macromolecular Structure Prediction" class="event-image">
            <div class="event-content">
                <h3 class="event-title">Macromolecular Structure Prediction to Guide Studies of RNA Virus Proteins</h3>
                <div class="event-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                    May 5 | 10:00 AM – 12:00 PM
                </div>
                <p class="event-description">A technical lecture on the latest advancements in macromolecular structure prediction and its applications in studying RNA virus proteins.</p>
            </div>
        </div>

        <div class="event-card">
            <img src="{{ asset('img/events/magnets.jpg') }}" alt="Exploring Rare-Earth-free Permanent Magnets" class="event-image">
            <div class="event-content">
                <h3 class="event-title">Exploring Rare-Earth-free Permanent Magnets for Electric Motors</h3>
                <div class="event-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                    May 5 | 1:30 PM – 3:30 PM
                </div>
                <p class="event-description">An engineering seminar discussing sustainable alternatives to rare-earth magnets in electric motor applications.</p>
            </div>
        </div>

        <div class="event-card">
            <img src="{{ asset('img/events/language.jpg') }}" alt="Seeping Through, Shaping Truth" class="event-image">
            <div class="event-content">
                <h3 class="event-title">Seeping Through, Shaping Truth: Contesting the Politics of Language, Identity, and Representation</h3>
                <div class="event-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                    May 5 | 9:00 AM – 3:30 PM
                </div>
                <p class="event-description">A humanities symposium exploring the intersection of language, identity politics, and representation in modern media.</p>
            </div>
        </div>

        <div class="event-card">
            <img src="{{ asset('img/events/tatak.jpg') }}" alt="Tatak Kyusi" class="event-image">
            <div class="event-content">
                <h3 class="event-title">Tatak Kyusi: Weaving Tourism, Local Enterprise and Artisans Fair</h3>
                <div class="event-date">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                    May 6 – May 9 | 8:00 AM – 5:00 PM
                </div>
                <p class="event-description">A week-long fair showcasing local artisans, entrepreneurs, and tourism initiatives from the Kyusi region.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
