import React from "react";
import { Head, Link, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import InputError from "@/Components/InputError.jsx";
import PrimaryButton from "@/Components/PrimaryButton.jsx";
import {
    Post,
    PostActions,
    PostAvatar,
    PostBody,
    PostContent,
    PostHeader,
} from "@/Components/Post.jsx";
import ScrollTop from "@/Components/ScrollTop.jsx";

export default function Index({ auth, Posts }) {
    const { data, setData, post, processing, reset, errors } = useForm({
        message: "",
        image: null,
    });

    const submit = (e) => {
        e.preventDefault();
        const formData = new FormData();
        formData.append("message", data.message);
        formData.append("image", data.image);
        post(route("posts.store"), {
            data: formData,
            onSuccess: () => {
                reset("message");
            },
        });
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <div className="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8">
                <form onSubmit={submit} encType="multipart/form-data">
                    <textarea
                        value={data.message}
                        required
                        placeholder={"Share with your ideas"}
                        onChange={(e) => setData("message", e.target.value)}
                        className="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                    ></textarea>
                    <InputError error={errors.message} className="mt-2" />

                    <label
                        htmlFor="image"
                        className="block text-sm font-medium text-gray-700 mt-4"
                    >
                        Upload Image
                    </label>
                    <input
                        id="image"
                        type="file"
                        accept="image/*"
                        onChange={(e) => setData("image", e.target.files[0])}
                        className="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                    />

                    <PrimaryButton className="mt-4" disabled={processing}>
                        Post
                    </PrimaryButton>
                </form>
                <div className="mt-6 bg-white shadow-sm rounded-lg divide-y">
                    {Posts.data.map((Post) => (
                        <Post key={Post.id} Post={Post}>
                            <PostAvatar />
                            <PostContent>
                                <PostHeader />
                                <Link
                                    href={route("posts.show", [
                                        Post.rePosting || Post.id,
                                    ])}
                                    className="mt-3 test-gray-900 flex flex-col gap-2"
                                >
                                    {Post.image_path && (
                                        <img
                                            src={`/storage/${Post.image_path}`}
                                            alt="Post Image"
                                            className="mt-2 rounded-md"
                                            style={{
                                                width: "350px",
                                                height: "450px",
                                            }}
                                        />
                                    )}
                                </Link>
                                <PostBody />
                                <PostActions />
                            </PostContent>
                        </Post>
                    ))}
                </div>
            </div>

            <ScrollTop />
        </AuthenticatedLayout>
    );
}
