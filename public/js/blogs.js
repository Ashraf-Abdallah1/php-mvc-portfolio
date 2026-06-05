
 src="https://cdn.jsdelivr.net/npm/chart.js">

function calculateReadingTime(text) {
    const words = text.trim().split(/\s+/).length;
    return Math.max(1, Math.ceil(words / 100));
}

async function loadBlogs() {
    try {
        const response = await fetch('/api/blogs');
        const json = await response.json();

        const container = document.getElementById('blog-list');
        container.innerHTML = "";

        if (!json.data || json.data.length === 0) {
            container.innerHTML = ` 
                <div class="alert alert-info shadow-sm border-0"> 
                    Er zijn nog geen blogs toegevoegd. 
                </div>`;
            return;
        }

        const titles = [];
        const wordCounts = [];

        json.data.forEach(blog => {

            const readingTime = calculateReadingTime(blog.content);

            titles.push(blog.title);
            wordCounts.push(blog.content.trim().split(/\s+/).length);

            const card = ` 
                <div class="card shadow-lg border-0 rounded-4 mb-4"> 
                    <div class="card-body p-4"> 

                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 mb-3 d-inline-block"> 
                             ${readingTime} min leestijd 
                        </span> 

                        <div class="d-flex align-items-center mb-4"> 
                            <div class="rounded-circle bg-success bg-opacity-10 d-flex justify-content-center align-items-center" 
                                 style="width: 70px; height: 70px;"> 
                                <i class="bi bi-journal-text text-success" style="font-size: 2rem;"></i> 
                            </div> 

                            <div class="ms-3"> 
                                <h4 class="fw-bold mb-0">${blog.title}</h4> 
                                <span class="text-muted">Datum: ${new Date(blog.date * 1000).toLocaleString()}</span> 
                            </div> 
                        </div> 

                        <hr> 

                        <div class="mb-4"> 
                            <h6 class="text-dark fw-semibold mb-2">Content</h6> 
                            <p class="text-muted fs-6">${blog.content}</p> 
                        </div> 

                        <hr> 

                        <div class="d-flex justify-content-end gap-3 mt-4"> 
                            <a href="/blogs/${blog.id}" class="btn btn-success px-4 shadow-sm"> 
                                <i class="bi bi-eye"></i> View 
                            </a> 
                        </div> 

                    </div> 
                </div> 
            `;

            container.innerHTML += card;
        });

        drawBlogChart(titles, wordCounts);

    } catch (error) {
        document.getElementById('blog-list').innerHTML = ` 
            <div class="alert alert-danger shadow-sm border-0"> 
                Fout bij het laden van blogs. 
            </div>`;
        console.error(error);
    }
}

function drawBlogChart(titles, wordCounts) {
    const ctx = document.getElementById('blogChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: titles,
            datasets: [{
                label: 'Aantal woorden per blog',
                data: wordCounts,
                backgroundColor: 'rgba(40, 167, 69, 0.4)',
                borderColor: '#28a745',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Blog Statistieken — Woordenaantal'
                }
            }
        }
    });
}

document.addEventListener("DOMContentLoaded", loadBlogs);
