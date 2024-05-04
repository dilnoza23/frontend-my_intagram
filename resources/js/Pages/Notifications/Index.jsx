import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.jsx";
import {Head} from "@inertiajs/react";
import Notification from "@/Components/Notification.jsx";
import {IconRePost, IconReply, IconUserFollows} from "@/Components/Icons.jsx";

export default function Index({auth, notifications}){
    function getNotificationProps(notification){
        switch(notification.type){
            case 'App\\Notifications\\LikePost':
                return {
                    'icon':'❤️',
                    'title':`${notification.data.liker.name} liked your post.`,
                    'body':`${notification.data.Post.message}`.substring(0,50)
                }
            case 'App\\Notifications\\NewFollower':
            return {
                'icon':<IconUserFollows />,
                'title':`${notification.data.follower.name} followed you.`,
                'body':'They will be notified of your new Posts'
            }
            case 'App\\Notifications\\NewPost':
                return {
                    'icon':'🐦',
                    'title':`${notification.data.user.name} created a new Post.`,
                    'body':`${notification.data.Post.message}`.substring(0,50)
                }
            case 'App\\Notifications\\ReplyPost':
                console.log(notification)
                return {
                    'icon': <div className='flex items-center'>
                        <IconReply />
                    </div>,
                    'title': `${notification.data.replier.name} replied to your Post: ${notification.data.Post.message.substring(0, 50)}`,
                    'body': `${notification.data.reply.message}`.substring(0, 50)
                }
            case 'App\\Notifications\\RePostPost':
                return {
                    'icon':<IconRePost />,
                    'title':`${notification.data.rePoster.name} rePosted your Post`,
                    'body':`${notification.data.rePost.message}`.substring(0,50)
                }
            default:
                return null;
        }
    }

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Notifications"/>
            <div className="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
                {notifications.length > 0
                    ?

                    <div className="bg-white shadow-sm rounded-lg divide-y">
                        {notifications.map((notification) => (
                            <Notification key={notification.id} notification={notification} {...getNotificationProps(notification)}/>
                        ))}
                    </div>
                    :

                    <div className='mt-6 max-w-sm mx-auto flex flex-col gap-4 items-center justify-center text-center'>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" className="w-20 h-20">
                            <path fill="#888888"
                                  d="M16.88 18.77H5.5q-.213 0-.356-.145Q5 18.481 5 18.27q0-.213.144-.356q.143-.144.356-.144h1.115V9.846q0-.575.126-1.156q.126-.582.378-1.11l.77.77q-.137.363-.205.737q-.069.374-.069.76v7.922h8.354L3.023 4.9q-.16-.134-.16-.341t.16-.367q.16-.16.354-.16q.194 0 .354.16L20.308 20.77q.14.14.153.342q.012.2-.157.37q-.156.156-.35.156t-.354-.16zm.505-3.812l-1-1V9.846q0-1.823-1.281-3.104q-1.28-1.28-3.104-1.28q-.832 0-1.6.286q-.77.287-1.365.86l-.72-.72q.558-.515 1.239-.863q.68-.348 1.446-.479V4q0-.417.291-.708q.291-.292.707-.292q.415 0 .709.292Q13 3.583 13 4v.546q1.923.327 3.154 1.824q1.23 1.497 1.23 3.476zm-5.388 6.427q-.668 0-1.14-.475q-.472-.474-.472-1.14h3.23q0 .67-.475 1.142q-.476.473-1.143.473m.713-11.102"/>
                        </svg>

                        <div>
                            <div className='text-lg font-semibold'>
                                No notifications yet
                            </div>
                            <div>
                                You'll be notified of new Posts, followers and interactions to your Posts
                            </div>
                        </div>
                    </div>
                }

            </div>
        </AuthenticatedLayout>

    )
}
