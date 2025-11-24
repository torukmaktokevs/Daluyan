import './bootstrap';
// Guarded landing-page script: run only on pages that include the expected DOM hooks
// Property Data (used only when a property grid exists on the page and is NOT server-rendered)
    const properties = [
            {
                id: 1,
                title: "Luxury Apartment in Bandra",
                type: "apartment",
                status: "sale",
                price: "₹1.2 Cr",
                location: "Bandra, Mumbai",
                description: "Beautiful 3BHK apartment with sea view and modern amenities",
                image: "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
                beds: 3,
                baths: 2,
                area: "1200 sq ft"
            },
            {
                id: 2,
                title: "Modern Villa in Bangalore",
                type: "villa",
                status: "sale",
                price: "₹3.5 Cr",
                location: "Whitefield, Bangalore",
                description: "Spacious 4BHK villa with private garden and swimming pool",
                image: "https://images.unsplash.com/photo-1613977257363-707ba9348227?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
                beds: 4,
                baths: 3,
                area: "2800 sq ft"
            },
            {
                id: 3,
                title: "Downtown Apartment for Rent",
                type: "apartment",
                status: "rent",
                price: "₹45,000/mo",
                location: "Connaught Place, Delhi",
                description: "Fully furnished 2BHK apartment in prime location",
                image: "https://images.unsplash.com/photo-1493809842364-78817add7ffb?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
                beds: 2,
                baths: 2,
                area: "950 sq ft"
            },
            {
                id: 4,
                title: "Beach House in Goa",
                type: "house",
                status: "sale",
                price: "₹2.8 Cr",
                location: "Calangute, Goa",
                description: "Stunning beachfront property with panoramic ocean views",
                image: "https://images.unsplash.com/photo-1512917774080-9991f1c4c750?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
                beds: 3,
                baths: 3,
                area: "2000 sq ft"
            },
            {
                id: 5,
                title: "Penthouse with Terrace",
                type: "apartment",
                status: "sale",
                price: "₹4.2 Cr",
                location: "Juhu, Mumbai",
                description: "Luxurious penthouse with private terrace and jacuzzi",
                image: "https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
                beds: 4,
                baths: 3,
                area: "2200 sq ft"
            },
            {
                id: 6,
                title: "Family Home in Hyderabad",
                type: "house",
                status: "rent",
                price: "₹35,000/mo",
                location: "Gachibowli, Hyderabad",
                description: "Well-maintained 3BHK house with garden and parking",
                image: "https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80",
                beds: 3,
                baths: 2,
                area: "1500 sq ft"
            }
        ];

    // DOM Elements (may not exist on every page)
    const propertyGrid = document.getElementById('propertyGrid');
    const isServerRendered = !!(propertyGrid && propertyGrid.querySelector('.property-card'));
    const filterBtns = document.querySelectorAll('.filter-btn');
    const mobileMenu = document.getElementById('mobileMenu');
    const loginModal = document.getElementById('loginModal');
    const loginModalClose = document.getElementById('loginModalClose');
    const navMenu = document.getElementById('navMenu');

        // Initialize the page
        function init() {
            // Only render and bind property-grid-specific handlers if the grid exists AND is not server-rendered
            if (propertyGrid && !isServerRendered) {
                renderProperties();
            }
            setupEventListeners();
        }

        // Render properties to the page
        function renderProperties(filter = 'all') {
            if (!propertyGrid) return; // page does not have a property grid
            propertyGrid.innerHTML = '';
            
            const filteredProperties = filter === 'all' 
                ? properties 
                : properties.filter(property => 
                    property.status === filter || property.type === filter
                );
            
            filteredProperties.forEach(property => {
                const propertyCard = document.createElement('div');
                propertyCard.className = 'property-card';
                propertyCard.innerHTML = `
                    <div class="property-img">
                        <img src="${property.image}" alt="${property.title}">
                        <div class="property-tag">${property.status === 'sale' ? 'For Sale' : 'For Rent'}</div>
                        <div class="favorite-btn"><i class="far fa-heart"></i></div>
                    </div>
                    <div class="property-info">
                        <div class="price">${property.price}</div>
                        <h3>${property.title}</h3>
                        <p>${property.description}</p>
                        <div class="property-meta">
                            <div class="meta-item">
                                <i class="fas fa-bed"></i>
                                <span>${property.beds} Beds</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-bath"></i>
                                <span>${property.baths} Baths</span>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-vector-square"></i>
                                <span>${property.area}</span>
                            </div>
                        </div>
                    </div>
                `;
                propertyGrid.appendChild(propertyCard);
            });
            
            // Add event listeners to favorite buttons
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                btn.addEventListener('click', toggleFavorite);
            });
        }

        // Toggle favorite
        function toggleFavorite(e) {
            const btn = e.currentTarget;
            btn.classList.toggle('active');
            const icon = btn.querySelector('i');
            
            if (btn.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        }

        // Setup event listeners
        function setupEventListeners() {
            // Filter buttons (only if present)
            if (!isServerRendered && filterBtns && filterBtns.length) {
                filterBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        // Remove active class from all buttons
                        filterBtns.forEach(b => b.classList.remove('active'));
                        // Add active class to clicked button
                        btn.classList.add('active');
                        // Filter properties
                        renderProperties(btn.getAttribute('data-filter'));
                    });
                });
            }
            
            // Intercept property card clicks for guests (modal)
            if (propertyGrid && loginModal) {
                propertyGrid.addEventListener('click', (e) => {
                    const link = e.target.closest('a.requires-login');
                    if (link) {
                        e.preventDefault();
                        openLoginModal();
                    }
                });
            }

            // Close modal
            if (loginModal && loginModalClose) {
                loginModalClose.addEventListener('click', closeLoginModal);
                loginModal.addEventListener('click', (e) => {
                    if (e.target === loginModal) closeLoginModal();
                });
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') closeLoginModal();
                });
            }

            // Mobile menu toggle (only if both elements exist)
            if (mobileMenu && navMenu) {
                mobileMenu.addEventListener('click', () => {
                    navMenu.classList.toggle('show');
                });
            }
            
            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        window.scrollTo({
                            top: target.offsetTop - 80,
                            behavior: 'smooth'
                        });
                        
                        // Close mobile menu if open
                        if (navMenu) {
                            navMenu.classList.remove('show');
                        }
                    }
                });
            });
        }

        // Initialize the page when loaded
        window.addEventListener('DOMContentLoaded', init);

        function openLoginModal() {
            if (!loginModal) return;
            loginModal.style.display = 'flex';
            loginModal.setAttribute('aria-hidden', 'false');
        }

        function closeLoginModal() {
            if (!loginModal) return;
            loginModal.style.display = 'none';
            loginModal.setAttribute('aria-hidden', 'true');
        }